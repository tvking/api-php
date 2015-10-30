<?php

namespace GroupByInc\API;

use Exception;
use GroupByInc\API\Util\SerializerFactory;
use GroupByInc\API\Util\UriBuilder;
use Httpful\Mime;
use Httpful\Request;
use Httpful\Response;
use JMS\Serializer\Serializer;
use RuntimeException;

abstract class AbstractBridge
{
    const SEARCH = '/search';
    const CLUSTER = '/cluster';
    const REFINEMENTS = '/refinements';
    const RESULTS_CLASS = 'GroupByInc\API\Model\Results';
    const REFINEMENTS_RESULT_CLASS = 'GroupByInc\API\Model\RefinementsResult';
    const HTTP = 'http://';
    const HTTPS = 'https://';
    const COLON = ':';
    const MAX_TRIES = 3;
    const RETRY_TIMEOUT = 80000;

    /** @var string */
    private $clientKey;
    /** @var string */
    private $bridgeUrl;
    /** @var string */
    private $bridgeUrlCluster;
    /** @var string */
    private $bridgeRefinementsUrl;
    /** @var Serializer */
    private $serializer;

    /**
     * @param string $clientKey
     * @param string $baseUrl
     */
    function __construct($clientKey, $baseUrl)
    {
        $this->clientKey = $clientKey;
        $this->bridgeUrl = $baseUrl . self::SEARCH;
        $this->bridgeUrlCluster = $baseUrl . self::CLUSTER;
        $this->bridgeRefinementsUrl = $baseUrl . self::SEARCH . self::REFINEMENTS;

        $this->serializer = SerializerFactory::build();
    }

    /**
     * @param Query $query
     *
     * @return mixed
     */
    public function search($query)
    {
        $content = $query->getBridgeJson($this->clientKey);

        return $this->query($this->bridgeUrl, $content, self::RESULTS_CLASS);
    }

    /**
     * @param Query  $query
     * @param string $navigationName
     *
     * @return mixed
     */
    public function refinements($query, $navigationName)
    {
        $content = $query->getBridgeRefinementsJson($this->clientKey, $navigationName);

        return $this->query($this->bridgeRefinementsUrl, $content, self::REFINEMENTS_RESULT_CLASS);
    }

    /**
     * @param string $url
     * @param string $content
     * @param string $class
     *
     * @return mixed
     */
    private function query($url, $content, $class)
    {
        $response = null;
        $tries = 0;
        $lastError = null;

        while ($tries < self::MAX_TRIES) {
            try {
                $response = $this->execute($url, $content, $tries);
                break;
            } catch (Exception $e) {
                usleep(self::RETRY_TIMEOUT);
                error_log('Connection failed, retrying');
                $lastError = $e;
                $tries++;
            }
        }

        if ($tries == self::MAX_TRIES) {
            throw new RuntimeException("Error: call to URL $url failed after " . self::MAX_TRIES . " tries", 0, $lastError);
        }

        if ($response->hasErrors()) {
            throw new RuntimeException("Error: call to URL $url failed with status $response->code, response $response");
        }

        if ($response->content_type !== Mime::JSON) {
            throw new RuntimeException("Error: bridge at URL $url did not return the expected JSON response, it returned: " . $response->content_type . " instead");
        }

        $responseBody = $response->raw_body;
        if (strpos($this->getContentEncoding($response), 'gzip') !== FALSE) {
            $responseBody = gzdecode($responseBody);
        }

        return $this->deserialize($responseBody, $class);
    }

    /**
     * @param string $url
     * @param string $content
     * @param int    $tries
     *
     * @return Response
     */
    protected function execute($url, $content, $tries)
    {
        echo "sending request $content to $url";

        return Request::post($url . "?retry=$tries")
            ->body($content)
            ->sendsType(Mime::JSON)
            ->send();
    }

    /**
     * @param Response $response
     *
     * @return bool|string
     */
    private function getContentEncoding($response)
    {
        $headers = $response->headers;
        foreach ($headers as $header) {
            list($k, $v) = explode(':', $header);
            if ('content-encoding' == strtolower($k)) {
                return trim($v);
            }
        }
        return false;
    }

    private function deserialize($json, $class)
    {
        $object = null;
        try {
            $object = $this->serializer->deserialize($json, $class, 'json');
        } catch (RuntimeException $e) {
            error_log("deserialization failed with exception $e");
        }
        return $object;
    }

}

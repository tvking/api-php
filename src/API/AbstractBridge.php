<?php

namespace GroupByInc\API;

use GroupByInc\API\Util\SerializerFactory;
use Httpful\Mime;
use Httpful\Request;
use Httpful\Response;
use JMS\Serializer\Serializer;
use RuntimeException;

abstract class AbstractBridge
{
    const SEARCH = '/search';
    const CLUSTER = '/cluster';
    const HTTP = 'http://';
    const HTTPS = 'https://';
    const COLON = ':';

    /** @var string */
    private $clientKey;
    /** @var string */
    private $bridgeUrl;
    /** @var string */
    private $bridgeUrlCluster;
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

        $this->serializer = SerializerFactory::build();
    }

    /**
     * @param Query $query
     * @return mixed
     */
    public function search($query)
    {
        $content = $query->getBridgeJson($this->clientKey);

        $response = $this->execute($this->bridgeUrl, $content);

        if ($response->hasErrors()) {
            throw new RuntimeException("Error: call to URL $this->bridgeUrl failed with status $response->code, response $response");
        }

        if ($response->content_type !== Mime::JSON) {
            throw new RuntimeException("Error: bridge at URL $this->bridgeUrl did not return the expected JSON response, it returned: " . $response->content_type . " instead");
        }

        $responseBody = $response->raw_body;
        if (strpos($this->getContentEncoding($response), 'gzip') !== FALSE) {
            $responseBody = gzdecode($responseBody);
        }

        return $this->deserialize($responseBody);
    }

    /**
     * @param string $url
     * @param string $content
     * @return Response
     */
    protected function execute($url, $content)
    {
        return Request::post($url)
            ->body($content)
            ->sendsType(Mime::JSON)
            ->send();
    }

    /**
     * @param Response $response
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

    private function deserialize($json)
    {
        $object = null;
        try {
            $object = $this->serializer->deserialize($json, 'GroupByInc\API\Model\Results', 'json');
        } catch (RuntimeException $e) {
            // should do something here
        }
        return $object;
    }

}

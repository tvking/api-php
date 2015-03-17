<?php

namespace GroupByInc\API;

use GroupByInc\API\Request\AbstractRequest;
use GroupByInc\API\Request\CurlRequest;
use RuntimeException;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Serializer;

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
    /** @var CurlRequest */
    private $session;
    /** @var CurlRequest */
    private $sessionCluster;
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

        $this->session = new CurlRequest($this->bridgeUrl);

        $this->session->setOption(CURLOPT_HEADER, false)
            ->setOption(CURLOPT_RETURNTRANSFER, true)
            ->setOption(CURLOPT_HTTPHEADER, array("Content-type: application/json"))
            ->setOption(CURLOPT_POST, true);

        $this->sessionCluster = new CurlRequest($this->bridgeUrlCluster);

        $this->sessionCluster->setOption(CURLOPT_HEADER, false)
            ->setOption(CURLOPT_RETURNTRANSFER, true)
            ->setOption(CURLOPT_HTTPHEADER, array("Content-type: application/json"))
            ->setOption(CURLOPT_POST, true);

        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
     * @param Query $query
     * @return mixed
     */
    public function search($query)
    {
        $content = $query->getBridgeJson($this->clientKey);
        $this->session->setOption(CURLOPT_POSTFIELDS, $content);
        if ($query->getCompressResponse()) {
            $this->session->setOption(CURLOPT_ENCODING, 'gzip');
        } else {
            $this->session->setOption(CURLOPT_ENCODING, '');
        }

        $json_response = $this->session->execute();
        $status = $this->session->getHttpStatusCode();
        $type = $this->session->getHttpContentType();

        if ($json_response === false || $status != 200) {
            throw new RuntimeException("Error: call to URL $this->bridgeUrl failed with status $status, response $json_response, curl_error " . $this->session->getError() . ", curl_errno " . $this->session->getErrorNumber());
        }

        if (!strstr($type, 'application/json')) {
            throw new RuntimeException("Error: bridge at URL $this->bridgeUrl did not return the expected JSON response, it returned: " . $type . " instead");
        }

        $responseBody = $this->getBody($json_response);
        if (strpos($this->getContentEncoding($json_response), 'gzip') !== FALSE) {
            $responseBody = gzdecode($responseBody);
        }

        return $this->deserialize($responseBody);
    }

    private function getContentEncoding($response)
    {
        $headers = $this->getHeaders($response);
        $arr = explode("\r\n", trim($headers));
        foreach ($arr as $header) {
            list($k, $v) = explode(':', $header);
            if ('content-encoding' == strtolower($k)) {
                return trim($v);
            }
        }
        return false;
    }

    private function getHeaders($response)
    {
        $header_size = $this->session->getInfo(CURLINFO_HEADER_SIZE);
        return substr($response, 0, $header_size);
    }

    private function getBody($response)
    {
        $header_size = $this->session->getInfo(CURLINFO_HEADER_SIZE);
        return substr($response, $header_size);
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

    /**
     * @param AbstractRequest $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @param AbstractRequest $session
     */
    public function setSessionCluster($session)
    {
        $this->sessionCluster = $session;
    }
}

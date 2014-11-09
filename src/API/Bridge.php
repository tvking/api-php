<?php

namespace GroupByInc\API;

use GroupByInc\API\Request\AbstractRequest;
use GroupByInc\API\Request\CurlRequest;
use RuntimeException;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Serializer;

class Bridge
{
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
     * @param string $host
     * @param int    $port
     */
    function __construct($clientKey, $host, $port)
    {
        $this->clientKey = $clientKey;
        $this->bridgeUrl = "http://" . $host . ":" . $port . "/search";
        $this->bridgeUrlCluster = "http://" . $host . ":" . $port . "/cluster";

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

        $json_response = $this->session->execute();
        $status = $this->session->getHttpStatusCode();
        $type = $this->session->getHttpContentType();

        if ($json_response === false || $status != 200) {
            throw new RuntimeException("Error: call to URL $this->bridgeUrl failed with status $status, response $json_response, curl_error " . $this->session->getError() . ", curl_errno " . $this->session->getErrorNumber());
        }

        if (!strstr($type, 'application/json')) {
            throw new RuntimeException("Error: bridge at URL $this->bridgeUrl did not return the expected JSON response, it returned: " . $type . " instead");
        }

        return $this->deserialize($json_response);
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

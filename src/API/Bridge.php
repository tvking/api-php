<?php

namespace GroupByInc\API;

class Bridge extends AbstractBridge
{
    /**
     * @param string $clientKey
     * @param string $bridgeHost
     * @param int $bridgePort
     */
    function __construct($clientKey, $bridgeHost, $bridgePort)
    {
        parent::__construct($clientKey, self::HTTP . $bridgeHost . self::COLON . $bridgePort);
    }
}

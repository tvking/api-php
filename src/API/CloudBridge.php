<?php

namespace GroupByInc\API;

class CloudBridge extends AbstractBridge
{
    const DOT = '.';
    const CLOUD_HOST = 'groupbycloud.com';
    const CLOUD_PORT = 443;
    const CLOUD_PATH = '/api/v1';

    /**
     * @param string $clientKey
     * @param string $customerId
     */
    function __construct($clientKey, $customerId)
    {
        parent::__construct($clientKey, self::HTTPS . $customerId . self::DOT . self::CLOUD_HOST . self::COLON . self::CLOUD_PORT . self::CLOUD_PATH);
    }
}
<?php

namespace GroupByInc\API\Request;

class CurlRequest
{
    private $handle = null;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->handle = curl_init($url);
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
        return $this;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        return curl_exec($this->handle);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getInfo($name)
    {
        return curl_getinfo($this->handle, $name);
    }

    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->getInfo(CURLINFO_HTTP_CODE);
    }

    /**
     * @return string
     */
    public function getHttpContentType()
    {
        return $this->getInfo(CURLINFO_CONTENT_TYPE);
    }

    /**
     * @return string
     */
    public function getError()
    {
        return curl_error($this->handle);
    }

    /**
     * @return int
     */
    public function getErrorNumber()
    {
        return curl_errno($this->handle);
    }

    public function close()
    {
        curl_close($this->handle);
    }

    public function __destruct()
    {
        curl_close($this->handle);
    }
}

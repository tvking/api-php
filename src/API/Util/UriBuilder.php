<?php

namespace GroupByInc\API\Util;

class UriBuilder
{
    const SCHEME_DIVIDER = "://";
    const USER_INFO_DIVIDER = "@";
    const PORT_DIVIDER = ":";
    const PATH_DIVIDER = "/";
    const QUERY_DIVIDER = "?";
    const QUERY_PARAM_DIVIDER = "&";
    const QUERY_KEY_VALUE_DIVIDER = "=";
    const FRAGMENT_DIVIDER = "#";

    /** @var string */
    private $scheme;
    /** @var string */
    private $user;
    /** @var string */
    private $password;
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var string */
    private $path;
    /** @var string[] */
    private $queryParams = array();
    /** @var string */
    private $fragment;

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param string $scheme
     * @return UriBuilder
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return UriBuilder
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     * @return UriBuilder
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return UriBuilder
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return UriBuilder
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param string $append
     * @return UriBuilder
     */
    public function appendToPath($append)
    {
        $this->path .= $append;
        return $this;
    }

    /**
     * @param string[] $params
     * @return UriBuilder
     */
    public function setParameters(array $params)
    {
        $this->queryParams = $params;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getParameters()
    {
        return $this->queryParams;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param string $fragment
     * @return UriBuilder
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
        return $this;
    }

    public function setFromString($uriString)
    {
        $this->clean();

        $matches = array();

        $schemePattern = "@(.*)" . self::SCHEME_DIVIDER . ".*@";
        if (preg_match($schemePattern, $uriString, $matches)) {
            $this->scheme = $matches[1];
        }

        $userInfoPattern = "~(?:.*" . self::SCHEME_DIVIDER . ")?(.*[" . self::PORT_DIVIDER . "]?.*)" .
            self::USER_INFO_DIVIDER . ".*~";
        if (preg_match($userInfoPattern, $uriString, $matches)) {
            $userSplit = explode(self::PORT_DIVIDER, $matches[1]);
            if (!empty($userSplit[0])) {
                $this->user = $userSplit[0];
                if (count($userSplit) == 2 && $userSplit[1] != null) {
                    $this->password = $userSplit[1];
                }
            }
        }

        $hostPattern = "~(?:.*" . self::USER_INFO_DIVIDER . ")?([^" .
            self::PATH_DIVIDER . "]*)(?:" . self::PATH_DIVIDER . ".*)?~";
        if (preg_match($hostPattern, $uriString, $matches)) {
            $hostSplit = explode(self::PORT_DIVIDER, $matches[1]);
            $this->host = $hostSplit[0];
            if (count($hostSplit) == 2 && !empty($hostSplit[1])) {
                $this->port = $hostSplit[1];
            }
        }

        $pathPattern = "@(?:(?:.*" . self::SCHEME_DIVIDER . ")?[^" . self::PATH_DIVIDER . "]*)?(" .
            self::PATH_DIVIDER . "[^" . self::QUERY_DIVIDER . "]*)+(?:\\" . self::QUERY_DIVIDER . ".*)?@";
        if (preg_match($pathPattern, $uriString, $matches)) {
            $this->path = $matches[1];
        }

        $queryPattern = "~.*?\\" . self::QUERY_DIVIDER . "([^" . self::FRAGMENT_DIVIDER . "]*)(?:" .
            self::FRAGMENT_DIVIDER . ".*)?~";
        if (preg_match($queryPattern, $uriString, $matches)) {
            $params = explode(self::QUERY_PARAM_DIVIDER, $matches[1]);
            foreach ($params as $param) {
                $keyValue = explode(self::QUERY_KEY_VALUE_DIVIDER, $param);
                if (count($keyValue) == 2) {
                    $this->setParameter(urldecode($keyValue[0]), urldecode($keyValue[1]));
                }
            }
        }

        $fragmentPattern = "~.*" . self::FRAGMENT_DIVIDER . "(.*)~";
        if (preg_match($fragmentPattern, $uriString, $matches)) {
            $this->fragment = $matches[1];
        }
    }

    private function clean()
    {
        $this->scheme = null;
        $this->user = null;
        $this->password = null;
        $this->host = null;
        $this->port = 0;
        $this->path = null;
        $this->queryParams = array();
        $this->fragment = null;
    }

    /**
     * @param string $key
     * @param string $value
     * @return UriBuilder
     */
    public function setParameter($key, $value)
    {
        $this->queryParams[$key] = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function build()
    {
        $builder = new StringBuilder();
        if (!empty($this->scheme)) {
            $builder->append($this->scheme)
                ->append(self::SCHEME_DIVIDER);
        }
        if (!empty($this->user)) {
            $builder->append($this->getUserInfo())
                ->append(self::USER_INFO_DIVIDER);
        }
        $builder->append($this->host);
        if ($this->port > 0) {
            $builder->append(self::PORT_DIVIDER)
                ->append($this->port);
        }
        if (!empty($this->path)) {
            if (!StringUtils::startsWith($this->getPath(), self::PATH_DIVIDER)) {
                $builder->append(self::PATH_DIVIDER);
            }
            $builder->append($this->getPath());
        }
        if (count($this->queryParams) > 0) {
            $builder->append(self::QUERY_DIVIDER)
                ->append($this->getQuery());
        }
        if (!empty($this->fragment)) {
            $builder->append(self::FRAGMENT_DIVIDER)
                ->append($this->fragment);
        }

        return $builder->__toString();
    }

    /**
     * @return string
     */
    public function getUserInfo()
    {
        $builder = new StringBuilder();
        if (!empty($this->user)) {
            $builder->append($this->user);
            if (!empty($this->password)) {
                $builder->append(self::PORT_DIVIDER)
                    ->append($this->password);
            }
        }
        return $builder;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return UriBuilder
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        $builder = new StringBuilder();
        foreach ($this->queryParams as $key => $value) {
            if (!empty($key) && $value != null) {
                if ($builder->length() > 0) {
                    $builder->append(self::QUERY_PARAM_DIVIDER);
                }
                $builder->append(urlencode($key))
                    ->append(self::QUERY_KEY_VALUE_DIVIDER)
                    ->append(urlencode($value));
            }
        }
        return $builder->__toString();
    }
}
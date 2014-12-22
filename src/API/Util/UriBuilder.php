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

    private static $SCHEME_PATTERN = "@(.*)://.*@";
    private static $USER_INFO_PATTERN = "~(?:.*://)?(.*[:]?.*)@.*~";
    private static $HOST_PATTERN = "~(?:.*@)?([^/]*)(?:/.*)?~";
    private static $PATH_PATTERN = "@(?:(?:.*://)?[^/]*)?(/[^?]*)+(?:\\?.*)?@";
    private static $QUERY_PATTERN = "~.*?\\?([^#]*)(?:#.*)?~";
    private static $FRAGMENT_PATTERN = "~.*#(.*)~";

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

        self::executeIfMatches(function ($match) {
            $this->scheme = $match;
        }, self::$SCHEME_PATTERN, $uriString);

        self::executeIfMatches(function ($match) {
            self::splitAndExecute(function (array $matches) {
                if (!empty($matches[0])) {
                    $this->user = $matches[0];
                    if (count($matches) == 2 && $matches[1] != null) {
                        $this->password = $matches[1];
                    }
                }
            }, self::PORT_DIVIDER, $match);
        }, self::$USER_INFO_PATTERN, $uriString);

        self::executeIfMatches(function ($match) {
            self::splitAndExecute(function (array $matches) {
                $this->host = $matches[0];
                if (count($matches) == 2 && !empty($matches[1])) {
                    $this->port = $matches[1];
                }
            }, self::PORT_DIVIDER, $match);
        }, self::$HOST_PATTERN, $uriString);

        self::executeIfMatches(function ($match) {
            $this->path = $match;
        }, self::$PATH_PATTERN, $uriString);

        self::executeIfMatches(function ($match) {
            self::splitAndExecute(function (array $matches) {
                foreach ($matches as $param) {
                    self::splitAndExecute(function (array $keyValue) {
                        if (count($keyValue) == 2) {
                            $this->setParameter(urldecode($keyValue[0]), urldecode($keyValue[1]));
                        }
                    }, self::QUERY_KEY_VALUE_DIVIDER, $param);
                }
            }, self::QUERY_PARAM_DIVIDER, $match);
        }, self::$QUERY_PATTERN, $uriString);

        self::executeIfMatches(function ($match) {
            $this->fragment = $match;
        }, self::$FRAGMENT_PATTERN, $uriString);
    }

    private static function executeIfMatches($function, $pattern, $uriString)
    {
        $matches = [];
        if (preg_match($pattern, $uriString, $matches)) {
            $function($matches[1]);
        }
    }

    /**
     * @param $function
     * @param $divider
     * @param $string
     */
    private static function splitAndExecute($function, $divider, $string)
    {
        $params = explode($divider, $string);
        $function($params);
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
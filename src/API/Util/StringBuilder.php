<?php

namespace GroupByInc\API\Util;

class StringBuilder
{
    /** @var string */
    private $string;

    /**
     * @param string $seedString
     */
    public function __construct($seedString = "")
    {
        $this->string = $seedString;
    }

    /**
     * @param string $append
     * @return StringBuilder
     */
    public function append($append)
    {
        $this->string .= $append;
        return $this;
    }

    /**
     * @param string $char
     * @param int    $offset
     * @return int
     */
    public function indexOf($char, $offset = 0)
    {
        if ($offset < strlen($this->string)) {
            if (strpos($this->string, $char, $offset) !== false) {
                return strpos($this->string, $char, $offset);
            }
        }
        return -1;
    }

    /**
     * @param int    $index
     * @param string $value
     * @return StringBuilder
     */
    public function insert($index, $value)
    {
        $this->replace($index, 0, $value);
        return $this;
    }

    /**
     * @param int    $index
     * @param int    $length
     * @param string $replacement
     */
    public function replace($index, $length, $replacement)
    {
        $this->string = substr_replace($this->string, $replacement, $index, $length);
    }

    /**
     * @param int $index
     */
    public function deleteCharAt($index)
    {
        $front = substr($this->string, 0, $index);
        $back = substr($this->string, $index + 1);
        $this->string = $front . $back;
    }

    public function delete($index, $length = null)
    {
        if ($length != null) {
            $this->string = substr_replace($this->string, "", $index, $length);
        } else {
            $this->string = substr_replace($this->string, "", $index);
        }
    }

    /**
     * @param int $index
     * @return string
     */
    public function getCharAt($index)
    {
        return substr($this->string, $index, 1);
    }

    /**
     * @param int $index
     * @param int $length
     * @return string
     */
    public function substring($index, $length = null)
    {
        if ($length != null) {
            return substr($this->string, $index, $length);
        } else {
            return substr($this->string, $index);
        }
    }

    /**
     * @return int
     */
    public function length()
    {
        return strlen($this->string);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->string;
    }
}
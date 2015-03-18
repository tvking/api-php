<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class Metadata
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $key;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $value;

    /**
     * @return string The name of this metadata.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key Set the name of this key.
     * @return Metadata
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string The value associated with this key.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value Set the value.
     * @return Metadata
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


}
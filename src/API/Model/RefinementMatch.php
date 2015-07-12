<?php

namespace GroupByInc\API\Model\RefinementMatch;

use JMS\Serializer\Annotation as JMS;

class Value
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $value;
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $count;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Value
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return Value
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

}

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class RefinementMatch
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $name;
    /**
     * @var RefinementMatch\Value[]
     * @JMS\Type("array<GroupByInc\API\Model\RefinementMatch\Value>")
     */
    public $values;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return RefinementMatch
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return RefinementMatch\Value[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param RefinementMatch\Value[] $values
     *
     * @return RefinementMatch
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

}
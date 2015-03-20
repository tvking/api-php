<?php

namespace GroupByInc\API\Model;

use GroupByInc\API\Model\Refinement\Type;
use JMS\Serializer\Annotation as JMS;

class RefinementValue extends Refinement
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $value;

    /**
     * @return string The value of this refinement.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value Set the value.
     * @return RefinementValue
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function isRange()
    {
        return false;
    }

    public function toTildeString()
    {
        return "=" . $this->value;
    }

    /**
     * @return string The type of refinement;
     */
    public function getType()
    {
        return Type::Value;
    }
}
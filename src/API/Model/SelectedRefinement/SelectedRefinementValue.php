<?php

namespace GroupByInc\API\Model;

use GroupByInc\API\Model\Refinement\Type;
use JMS\Serializer\Annotation as JMS;

class SelectedRefinementValue extends SelectedRefinement
{
    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return SelectedRefinementValue
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
     * @return string
     */
    public function getType()
    {
        return Type::Value;
    }
}
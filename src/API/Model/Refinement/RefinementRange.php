<?php

namespace GroupByInc\API\Model;

use GroupByInc\API\Model\Refinement\Type;
use JMS\Serializer\Annotation as JMS;

class RefinementRange extends Refinement
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    private $high;
    /**
     * @var string
     * @JMS\Type("string")
     */
    private $low;

    /**
     * @return string The upper bound of this range.
     */
    public function getHigh()
    {
        return $this->high;
    }

    /**
     * @param string $high Set the uppermost value.
     * @return RefinementRange
     */
    public function setHigh($high)
    {
        $this->high = $high;
        return $this;
    }

    /**
     * @return string The lower bound of this range.
     */
    public function getLow()
    {
        return $this->low;
    }

    /**
     * @param string $low Set the lower bound.
     * @return RefinementRange
     */
    public function setLow($low)
    {
        $this->low = $low;
        return $this;
    }

    public function isRange()
    {
        return true;
    }

    public function toTildeString()
    {
        return ":" . $this->low . ".." . $this->high;
    }

    /**
     * @return string The type of refinement;
     */
    public function getType()
    {
        return Type::Range;
    }
}
<?php

namespace GroupByInc\API\Model;

use GroupByInc\API\Model\Refinement\Type;
use JMS\Serializer\Annotation as JMS;

class SelectedRefinementRange extends SelectedRefinement
{
    /**
     * @var string
     */
    private $high;
    /**
     * @var string
     */
    private $low;

    /**
     * @return string
     */
    public function getHigh()
    {
        return $this->high;
    }

    /**
     * @param string $high
     * @return SelectedRefinementRange
     */
    public function setHigh($high)
    {
        $this->high = $high;
        return $this;
    }

    /**
     * @return string
     */
    public function getLow()
    {
        return $this->low;
    }

    /**
     * @param string $low
     * @return SelectedRefinementRange
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
     * @return string
     */
    public function getType()
    {
        return Type::Range;
    }
}
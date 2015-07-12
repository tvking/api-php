<?php

namespace GroupByInc\API\Request\SelectedRefinement;

class Type
{
    const Range = 'Range';
    const Value = 'Value';
}

namespace GroupByInc\API\Request;

use GroupByInc\API\Request\SelectedRefinement\Type;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\Discriminator(field = "type", map = {
 *  "Range": "GroupByInc\API\Request\SelectedRefinementRange",
 *  "Value": "GroupByInc\API\Request\SelectedRefinementValue"
 * })
 */
abstract class SelectedRefinement
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $navigationName;
    /**
     * @var bool
     */
    private $exclude = false;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNavigationName()
    {
        return $this->navigationName;
    }

    /**
     * @param string $navigationName
     *
     * @return $this
     */
    public function setNavigationName($navigationName)
    {
        $this->navigationName = $navigationName;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isExclude()
    {
        return $this->exclude;
    }

    /**
     * @param boolean $exclude
     *
     * @return $this
     */
    public function setExclude($exclude)
    {
        $this->exclude = $exclude;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRange()
    {
        return $this->getType() === Type::Range;
    }

    /**
     * @return string
     */
    public abstract function getType();

    /**
     * @return string
     */
    public abstract function toTildeString();

}
<?php

namespace GroupByInc\API\Model\Refinement;

class Type
{
    const Range = 'Range';
    const Value = 'Value';
}

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\Discriminator(field = "type", map = {
 *  "Range": "GroupByInc\API\Model\RefinementRange",
 *  "Value": "GroupByInc\API\Model\RefinementValue"
 * })
 */
abstract class Refinement
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    private $id;
    /**
     * @var int
     * @JMS\Type("integer")
     */
    private $count;

    /**
     * @return string The ID is a MD5 of the name and value of the refinement.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id Set the ID.
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int The number of records that will be left if this Refinement is selected.
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count Set the count.
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return string The type of refinement;
     */
    public abstract function getType();

    /**
     * @return bool True if this is a Range Refinement.
     */
    public abstract function isRange();

    /**
     * @return string A string representation of the Refinement.
     */
    public abstract function toTildeString();

}
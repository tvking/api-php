<?php

namespace GroupByInc\API\Model\Navigation;

class Order
{
    const Count_Ascending = 'Count_Ascending';
    const Count_Descending = 'Count_Descending';
    const Value_Ascending = 'Value_Ascending';
    const Value_Descending = 'Value_Descending';
}

class Type
{
    const Date = 'Date';
    const Float = 'Float';
    const Integer = 'Integer';
    const String = 'String';
    const Range_Date = 'Range_Date';
    const Range_Integer = 'Range_Integer';
    const Range_Float = 'Range_Float';
}

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class Navigation
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("_id")
     */
    public $id;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $name;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $type;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $sort;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $displayName;
    /**
     * @var bool
     * @JMS\Type("boolean")
     */
    public $range = false;
    /**
     * @var bool
     * @JMS\Type("boolean")
     */
    public $or = false;
    /**
     * @var Refinement[]
     * @JMS\Type("array<GroupByInc\API\Model\Refinement>")
     */
    public $refinements = array();
    /**
     * @var Metadata[]
     * @JMS\Type("array<GroupByInc\API\Model\Metadata>")
     */
    public $metadata = array();

    /**
     * @return string The name of the dynamic navigation attribute.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name The name of the navigation.
     * @return Navigation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string The human readable label for this navigation.
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName Set the display name.
     * @return Navigation
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return Refinement[] A list of refinement values that represent the ways in which you can filter data.
     */
    public function &getRefinements()
    {
        return $this->refinements;
    }

    /**
     * @param Refinement[] $refinements The refinement values.
     * @return Navigation
     */
    public function setRefinements($refinements)
    {
        $this->refinements = $refinements;
        return $this;
    }

    /**
     * @return string A MD5 of the name, which means that this navigation ID is unique.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id Set the ID.
     * @return Navigation
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool True if this navigation is a of type range.
     */
    public function isRange()
    {
        return $this->range;
    }

    /**
     * @param bool $range Set range.
     * @return Navigation
     */
    public function setRange($range)
    {
        $this->range = $range;
        return $this;
    }

    /**
     * @return bool Is this dynamic navigation going to be treated as an OR field in the bridge layer.
     */
    public function isOr()
    {
        return $this->or;
    }

    /**
     * @param bool $or Set whether this is an OR field.
     * @return Navigation
     */
    public function setOr($or)
    {
        $this->or = $or;
        return $this;
    }

    /**
     * @return string The type of navigation.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type Set the type of navigation.
     * @return Navigation
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string The sort option for this navigation.
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param string $sort Set the sort type.
     * @return Navigation
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return Metadata[] A list of metadata elements.
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param Metadata[] $metadata Set the metadata.
     * @return Navigation
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

}
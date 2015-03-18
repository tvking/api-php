<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class Template
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
    public $ruleName;
    /**
     * @var Zone[]
     * @JMS\Type("array<GroupByInc\API\Model\Zone>")
     */
    public $zones = array();

    /**
     * @return string An MD5 hash of the name of this template.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id Set the ID.
     * @return Template
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string The name as set in the command center.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name Set the name.
     * @return Template
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string The name of the rule that triggered this template.
     */
    public function getRuleName()
    {
        return $this->ruleName;
    }

    /**
     * @param string $ruleName Set the rule.
     * @return Template
     */
    public function setRuleName($ruleName)
    {
        $this->ruleName = $ruleName;
        return $this;
    }

    /**
     * @return Zone[] A list of the zones in this template.
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * @param Zone[] $zones Set the zones.
     * @return Template
     */
    public function setZones($zones)
    {
        $this->zones = $zones;
        return $this;
    }

}
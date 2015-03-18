<?php

namespace GroupByInc\API\Model\Zone;

class Type
{
    const Content = 'Content';
    const Record = 'Record';
    const Banner = 'Banner';
    const Rich_Content = 'Rich_Content';
}

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\Discriminator(field = "type", map = {
 *  "Content": "GroupByInc\API\Model\ContentZone",
 *  "Record": "GroupByInc\API\Model\RecordsZone",
 *  "Banner": "GroupByInc\API\Model\BannerZone",
 *  "Rich_Content": "GroupByInc\API\Model\RichContentZone"
 * })
 */
abstract class Zone
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
     * @return string ID is a MD5 hash of the name.
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
     * @return string The name of the zone.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name Set the name.
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string The type of zone.
     */
    public abstract function getType();
}

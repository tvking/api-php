<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class Record
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
     * @JMS\SerializedName("_u")
     */
    public $url;
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("_snippet")
     */
    public $snippet;
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("_t")
     */
    public $title;
    /**
     * @var object[]
     * @JMS\Type("array")
     */
    public $allMeta = array();
    /**
     * @var RefinementMatch[]
     * @JMS\Type("array<GroupByInc\API\Model\RefinementMatch>")
     */
    public $refinementMatches = array();


    /**
     * @return string The ID is generated from URL of this record which means it will persist across updates.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id Set the ID.
     *
     * @return Record
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string The URL represents the Unique ID of the record.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url Set the record URL.
     *
     * @return Record
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string If a search was performed this record object may have a snippet of matching text.
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * @param string $snippet Set the snippet
     *
     * @return Record
     */
    public function setSnippet($snippet)
    {
        $this->snippet = $snippet;
        return $this;
    }

    /**
     * @param string $name Get a specific metadata value.
     *                     Essentially this represents an attribute of the record data.
     *
     * @return object The requested metadata value.
     */
    public function getMetaValue($name)
    {
        return $this->allMeta[$name];
    }

    /**
     * @return object[] A map of all the metadata associated with this record.
     */
    public function getAllMeta()
    {
        return $this->allMeta;
    }

    /**
     * @param object[] $allMeta Set the metadata.
     *
     * @return Record
     */
    public function setAllMeta($allMeta)
    {
        $this->allMeta = $allMeta;
        return $this;
    }

    /**
     * @return string The title of this record.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title Set the title.
     *
     * @return Record
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return RefinementMatch[]
     */
    public function getRefinementMatches()
    {
        return $this->refinementMatches;
    }

    /**
     * @param RefinementMatch[] $refinementMatches
     *
     * @return Record
     */
    public function setRefinementMatches($refinementMatches)
    {
        $this->refinementMatches = $refinementMatches;
        return $this;
    }

}
<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class ClusterRecord
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $title;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $url;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $snippet;

    /**
     * @return string The title of this record.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title The title of this record.
     * @return ClusterRecord
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string The Unique identifier of this record.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url The Unique identifier of this record.
     * @return ClusterRecord
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string The matching set of terms for this record.
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * @param string $snippet Snippet value.
     * @return ClusterRecord
     */
    public function setSnippet($snippet)
    {
        $this->snippet = $snippet;
        return $this;
    }

}
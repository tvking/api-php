<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class ContentZone extends Zone
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $content;

    /**
     * @return string If this zone is not a Record zone this will represent the value set by the merchandiser.
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content Set the content.
     *
     * @return ContentZone
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string The type of zone.
     */
    public function getType()
    {
        return Zone\Type::Content;
    }
}
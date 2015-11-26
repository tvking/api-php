<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class RichContentZone extends Zone
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $richContent;

    /**
     * @return string The value set by the merchandiser.
     */
    public function getRichContent()
    {
        return $this->richContent;
    }

    /**
     * @param string $richContent Set the rich content.
     *
     * @return RichContentZone
     */
    public function setRichContent($richContent)
    {
        $this->richContent = $richContent;
        return $this;
    }

    /**
     * @return string The type of zone.
     */
    public function getType()
    {
        return Zone\Type::Rich_Content;
    }
}
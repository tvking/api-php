<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class ContentZone extends AbstractContentZone
{
    /**
     * @return string
     */
    public function getContent()
    {
        return parent::getContent();
    }

    /**
     * @param string $content
     * @return ContentZone
     */
    public function setContent($content)
    {
        parent::setContent($content);
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
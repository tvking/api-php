<?php

namespace GroupByInc\API\Model;

class RichContentZone extends AbstractContentZone
{
    /**
     * @return string
     */
    public function getRichContent()
    {
        return $this->getContent();
    }

    /**
     * @param string $richContent
     * @return RichContentZone
     */
    public function setRichContent($richContent)
    {
        $this->setContent($richContent);
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
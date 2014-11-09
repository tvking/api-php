<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

abstract class AbstractContentZone extends Zone
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    private $content;

    /**
     * @return string If this zone is not a Record zone this will represent the value set by the merchandiser.
     */
    protected function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content Set the content.
     */
    protected function setContent($content)
    {
        $this->content = $content;
    }
}
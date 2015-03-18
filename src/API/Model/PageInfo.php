<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class PageInfo
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $recordStart;
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $recordEnd;

    /**
     * @return int The record offset for this search and navigation state.
     */
    public function getRecordStart()
    {
        return $this->recordStart;
    }

    /**
     * @param int $recordStart Set the record offset.
     * @return PageInfo
     */
    public function setRecordStart($recordStart)
    {
        $this->recordStart = $recordStart;
        return $this;
    }

    /**
     * @return int The index of the last record in this page of results.
     */
    public function getRecordEnd()
    {
        return $this->recordEnd;
    }

    /**
     * @param int $recordEnd Set the last record index.
     * @return PageInfo
     */
    public function setRecordEnd($recordEnd)
    {
        $this->recordEnd = $recordEnd;
        return $this;
    }

}
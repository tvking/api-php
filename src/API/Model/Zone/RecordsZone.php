<?php

namespace GroupByInc\API\Model;

use GroupByInc\API\Model\Zone\Type;
use JMS\Serializer\Annotation as JMS;

class RecordsZone extends Zone
{
    /**
     * @var Record[]
     * @JMS\Type("array<GroupByInc\API\Model\Record>")
     */
    public $records = array();

    /**
     * @return Record[] If this zone is a record zone, a list of records returned from the bridge.
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param Record[] $records Set the records.
     * @return RecordsZone
     */
    public function setRecords($records)
    {
        $this->records = $records;
        return $this;
    }

    /**
     * @return string The type of zone.
     */
    public function getType()
    {
        return Zone\Type::Record;
    }
}
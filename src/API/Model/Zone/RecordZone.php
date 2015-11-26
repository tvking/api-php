<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class RecordZone extends Zone
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $query;
    /**
     * @var Record[]
     * @JMS\Type("array<GroupByInc\API\Model\Record>")
     */
    public $records = array();

    /**
     * @return string The query that was fired for this zone.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query Set the query
     *
     * @return RecordZone
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @return Record[] If this zone is a record zone, a list of records returned from the bridge.
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param Record[] $records Set the records.
     *
     * @return RecordZone
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
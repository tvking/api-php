<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class Cluster
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $term;
    /**
     * @var ClusterRecord[]
     * @JMS\Type("array<GroupByInc\API\Model\ClusterRecord>")
     */
    public $records = array();

    /**
     * @return string The term for this cluster.
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param string $term The cluster term.
     * @return Cluster
     */
    public function setTerm($term)
    {
        $this->term = $term;
        return $this;
    }

    /**
     * @return ClusterRecord[] The list of clustered records.
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param ClusterRecord[] $records The list of cluster records to set.
     * @return Cluster
     */
    public function setRecords($records)
    {
        $this->records = $records;
        return $this;
    }

}
<?php
/**
 * Created by IntelliJ IDEA.
 * User: groupby
 * Date: 07/04/15
 * Time: 10:56 AM
 */

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class RestrictNavigation
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $name;

    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $count;

    /**
     * @param string $name Set the name of the field should be used in the navigation restriction in the second query.
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string The name of the field should be used in the navigation restriction in the second query.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int The number of fields matches
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count Set the tnumber of fields matches.
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }
}
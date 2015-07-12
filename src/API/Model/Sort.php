<?php

namespace GroupByInc\API\Model\Sort;

class Order
{
    const Ascending = 'Ascending';
    const Descending = 'Descending';
}

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class Sort
{
    /**
     * @var Sort
     *
     * @JMS\Exclude
     */
    public static $RELEVANCE;

    public static function init()
    {
        self::$RELEVANCE = new Sort();
        self::$RELEVANCE->setField('_relevance');
    }

    /**
     * @var string
     *
     * @JMS\Type("string")
     */
    private $field;
    /**
     * @var string
     *
     * @JMS\Type("string")
     */
    private $order = Sort\Order::Ascending;

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return Sort
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $order
     *
     * @return Sort
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

}

Sort::init();
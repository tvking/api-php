<?php

namespace GroupByInc\API\Request\Sort;

class Order
{
    const Ascending = 'Ascending';
    const Descending = 'Descending';
}

namespace GroupByInc\API\Request;

class Sort
{
    /** @var string */
    private $field;
    /** @var string */
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
     * @return Sort
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

}
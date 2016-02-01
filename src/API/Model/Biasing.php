<?php

namespace GroupByInc\API\Model;

class Biasing
{
    /**
     * @var string[]
     */
    public $bringToTop = array();

    /**
     * @return string[] The list of product IDs
     */
    public function getBringToTop()
    {
        return $this->bringToTop;
    }

    /**
     * A list of product IDs to bring to the top of the result set. This list
     * will ensure that the products are included in the result set and appear in the order
     * defined.
     *
     * @param string[] $bringToTop The list of productIds.
     *
     * @return Biasing
     */
    public function setBringToTop($bringToTop)
    {
        $this->bringToTop = $bringToTop;
        return $this;
    }
}
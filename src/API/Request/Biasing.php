<?php

namespace GroupByInc\API\Request;

class Biasing
{
    /** @var string[] */
    private $bringToTop = array();

    /**
     * @return string[]
     */
    public function getBringToTop()
    {
        return $this->bringToTop;
    }

    /**
     * @param string[] $bringToTop
     *
     * @return Biasing
     */
    public function setBringToTop($bringToTop)
    {
        $this->bringToTop = $bringToTop;
        return $this;
    }


}
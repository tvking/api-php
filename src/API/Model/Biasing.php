<?php

namespace GroupByInc\API\Model;

class Biasing
{
    /**
     * @var string[]
     */
    private $bringToTop = array();

    /**
     * @var bool
     */
    private $augmentBiases = false;

    /**
     * @var float|null
     */
    private $influence = null;

    /**
     * @var Bias[]
     */
    private $biases = array();


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

    /**
     * @return boolean
     */
    public function isAugmentBiases()
    {
        return $this->augmentBiases;
    }

    /**
     * @param boolean $augmentBiases
     *
     * @return Biasing
     */
    public function setAugmentBiases($augmentBiases)
    {
        $this->augmentBiases = $augmentBiases;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getInfluence()
    {
        return $this->influence;
    }

    /**
     * @param float|null $influence
     *
     * @return Biasing
     */
    public function setInfluence($influence)
    {
        $this->influence = $influence;
        return $this;
    }

    /**
     * @return Bias[]
     */
    public function getBiases()
    {
        return $this->biases;
    }

    /**
     * @param Bias[] $biases
     *
     * @return Biasing
     */
    public function setBiases($biases)
    {
        $this->biases = $biases;
        return $this;
    }
}
<?php

namespace GroupByInc\API\Request;

class Biasing
{
    /** @var string[] */
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
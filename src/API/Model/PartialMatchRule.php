<?php

namespace GroupByInc\API\Model;

class PartialMatchRule
{
    /** @var int */
    private $terms;
    /** @var int */
    private $termsGreaterThan;
    /** @var int */
    private $mustMatch;
    /** @var bool */
    private $percentage;

    /**
     * @return int
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * @param int $terms
     *
     * @return PartialMatchRule
     */
    public function setTerms($terms)
    {
        $this->terms = $terms;
        return $this;
    }

    /**
     * @return int
     */
    public function getTermsGreaterThan()
    {
        return $this->termsGreaterThan;
    }

    /**
     * @param int $termsGreaterThan
     *
     * @return PartialMatchRule
     */
    public function setTermsGreaterThan($termsGreaterThan)
    {
        $this->termsGreaterThan = $termsGreaterThan;
        return $this;
    }

    /**
     * @return int
     */
    public function getMustMatch()
    {
        return $this->mustMatch;
    }

    /**
     * @param int $mustMatch
     *
     * @return PartialMatchRule
     */
    public function setMustMatch($mustMatch)
    {
        $this->mustMatch = $mustMatch;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param boolean $percentage
     *
     * @return PartialMatchRule
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
        return $this;
    }

}
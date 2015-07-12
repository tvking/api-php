<?php

namespace GroupByInc\API\Model;

class MatchStrategy
{
    /** @var PartialMatchRule[] */
    public $rules;

    /**
     * @return PartialMatchRule[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param PartialMatchRule[] $rules
     *
     * @return MatchStrategy
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
        return $this;
    }
}
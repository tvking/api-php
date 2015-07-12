<?php

namespace GroupByInc\API\Request;

class MatchStrategy
{
    /** @var PartialMatchRule[] */
    private $rules;

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
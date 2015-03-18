<?php

namespace GroupByInc\API\Url;

use GroupByInc\API\Util\StringBuilder;
use GroupByInc\API\Model\SelectedRefinement;

class UrlReplacementRule
{
    /** @var string */
    private $target;
    /** @var string */
    private $replacement;
    /** @var string */
    private $navigationName;

    /**
     * @param string $target
     * @param string $replacement
     * @param string $refinementName
     */
    function __construct($target, $replacement, $refinementName)
    {
        $this->target = $target;
        if ($replacement == null) {
            $this->replacement = "";
        } else {
            $this->replacement = $replacement;
        }
        $this->navigationName = $refinementName;
    }

    /**
     * @param StringBuilder    $builder
     * @param int              $indexOffset
     * @param string           $navigationName
     * @param UrlReplacement[] $replacementBuilder
     */
    public function apply(StringBuilder $builder, $indexOffset, $navigationName, array
        &$replacementBuilder)
    {
        if ($builder->length() > 0 && ($this->navigationName == null || $navigationName == $this->navigationName)) {
            $index = $builder->indexOf($this->target);
            while ($index != -1) {
                $op = OperationType::Swap;
                if (empty($this->replacement)) {
                    $this->replacement = "";
                    $op = OperationType::Insert;
                    $builder->deleteCharAt($index);
                } else {
                    $builder->replace($index, strlen($this->target), $this->replacement);
                }
                $replacement = new UrlReplacement($index + $indexOffset, $this->target, $op);
                array_push($replacementBuilder, $replacement);
                $index = $builder->indexOf($this->target, $index);
            }
        }
    }
}
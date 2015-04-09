<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class RefinementsResult
{
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $errors;
    /**
     * @var Navigation
     * @JMS\Type("GroupByInc\API\Model\Navigation")
     */
    public $navigation;

    /**
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return Navigation
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * @param Navigation $navigation
     */
    public function setNavigation($navigation)
    {
        $this->navigation = $navigation;
    }
}


<?php

namespace GroupByInc\API\Request\Bias;

class Strength
{
    const Absolute_Increase = "Absolute_Increase";
    const Strong_Increase = "Strong_Increase";
    const Medium_Increase = "Medium_Increase";
    const Weak_Increase = "Weak_Increase";
    const Leave_Unchanged = "Leave_Unchanged";
    const Weak_Decrease = "Weak_Decrease";
    const Medium_Decrease = "Medium_Decrease";
    const Strong_Decrease = "Strong_Decrease";
    const Absolute_Decrease = "Absolute_Decrease";
}

namespace GroupByInc\API\Request;

class Bias
{

    /** @var string */
    private $name;

    /** @var string */
    private $content;

    /** @var string */
    private $strength;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Bias
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return Bias
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * @param string $strength
     *
     * @return Bias
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;
        return $this;
    }
}

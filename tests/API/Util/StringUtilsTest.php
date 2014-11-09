<?php

use GroupByInc\API\Util\StringUtils;

class StringUtilsTest extends PHPUnit_Framework_TestCase
{
    private $string = "this is a string";

    public function testStartsWithCharacter()
    {
        $this->assertTrue(StringUtils::startsWith($this->string, "t"));
    }

    public function testStartsWithSnippet()
    {
        $this->assertTrue(StringUtils::startsWith($this->string, "this is"));
    }

    public function testDoesNotStartWith()
    {
        $this->assertFalse(StringUtils::startsWith($this->string, "jellybean"));
        $this->assertFalse(StringUtils::startsWith($this->string, "his"));
        $this->assertFalse(StringUtils::startsWith($this->string, "tag"));
    }

    public function testEndsWithCharacter()
    {
        $this->assertTrue(StringUtils::endsWith($this->string, "g"));
    }

    public function testEndsWithSnippet()
    {
        $this->assertTrue(StringUtils::endsWith($this->string, "a string"));
    }

    public function testDoesNotEndWith()
    {
        $this->assertFalse(StringUtils::endsWith($this->string, "nonsense"));
        $this->assertFalse(StringUtils::endsWith($this->string, "strin"));
        $this->assertFalse(StringUtils::endsWith($this->string, "strung"));
    }

}

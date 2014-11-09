<?php

use GroupByInc\API\Util\ArrayUtils;

class ArrayUtilsTest extends PHPUnit_Framework_TestCase
{
    /** @var string[] */
    private $array;

    public function setUp()
    {
        $this->array = array("a", "bunch", "of", "words", "in", "an", "array");;
    }

    public function testRemoveFromArrayByIndex()
    {
        ArrayUtils::removeByIndex($this->array, 0);
        $this->assertEquals(6, count($this->array));
        $this->assertEquals(array("bunch", "of", "words", "in", "an", "array"), $this->array);

        ArrayUtils::removeByIndex($this->array, 2);
        $this->assertEquals(5, count($this->array));
        $this->assertEquals(array("bunch", "of", "in", "an", "array"), $this->array);
    }

    public function testGetElementOnRemoveByIndex()
    {
        $this->assertEquals("a", ArrayUtils::removeByIndex($this->array, 0));
        $this->assertEquals("bunch", ArrayUtils::removeByIndex($this->array, 0));
        $this->assertEquals("of", ArrayUtils::removeByIndex($this->array, 0));
    }

    public function testRemoveFromArray()
    {
        ArrayUtils::remove($this->array, "a");
        $this->assertEquals(6, count($this->array));
        $this->assertEquals(array("bunch", "of", "words", "in", "an", "array"), $this->array);

        ArrayUtils::remove($this->array, "words");
        $this->assertEquals(5, count($this->array));
        $this->assertEquals(array("bunch", "of", "in", "an", "array"), $this->array);
    }

}

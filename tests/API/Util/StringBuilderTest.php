<?php

use GroupByInc\API\Util\StringBuilder;

class StringBuilderTest extends PHPUnit_Framework_TestCase
{
    /** @var StringBuilder */
    private $builder;

    public function setUp()
    {
        $this->builder = new StringBuilder("some words");
    }

    public function testConstruction()
    {
        $builder = new StringBuilder();
        $this->assertEquals("", $builder);

        $builder = new StringBuilder("test");
        $this->assertEquals("test", $builder);
    }

    public function testAppend()
    {
        $this->builder->append(" and")->append(" some")->append(" more");
        $this->assertEquals("some words and some more", $this->builder);
    }

    public function testIndexOf()
    {
        $this->assertEquals(0, $this->builder->indexOf("some"));
        $this->assertEquals(1, $this->builder->indexOf("ome"));
        $this->assertEquals(6, $this->builder->indexOf("o", 2));
        $this->assertEquals(-1, $this->builder->indexOf("whale"));
    }

    public function testInsert()
    {
        $this->builder->insert(0, "before ");
        $this->assertEquals("before some words", $this->builder);

        $this->builder->insert(12, "other ");
        $this->assertEquals("before some other words", $this->builder);
    }

    public function testReplace()
    {
        $this->builder->replace(0, 4, "a whole bunch of");
        $this->assertEquals("a whole bunch of words", $this->builder);

        $this->builder->replace(0, strlen($this->builder), "solitude");
        $this->assertEquals("solitude", $this->builder);
    }

    public function testDeleteCharAt()
    {
        $this->builder->deleteCharAt(0);
        $this->assertEquals("ome words", $this->builder);

        $this->builder->deleteCharAt(4);
        $this->assertEquals("ome ords", $this->builder);
    }

    public function testSubstring()
    {
        $this->assertEquals("some words", $this->builder->substring(0, strlen($this->builder)));
        $this->assertEquals("some", $this->builder->substring(0, 4));
        $this->assertEquals("words", $this->builder->substring(5));
    }

}

<?php

use GroupByInc\API\Util\UriBuilder;

class UriBuilderTest extends PHPUnit_Framework_TestCase
{
    /** @var UriBuilder */
    private $builder;

    public function setUp()
    {
        $this->builder = new UriBuilder();
        $this->builder->setScheme("http")
            ->setUser("guest")
            ->setPassword("password")
            ->setHost("example.com")
            ->setPort(8080)
            ->setPath("some/wild/path")
            ->setParameter("sale", "true")
            ->setParameter("columns", "4")
            ->setFragment("bookmark");
    }

    public function testSetPath()
    {
        $newPath = "a/completely/different/path";
        $this->builder->setPath($newPath);
        $this->assertEquals($newPath, $this->builder->getPath());
    }

    public function testAppendToPath()
    {
        $this->builder->appendToPath("/to")
            ->appendToPath("/our")
            ->appendToPath("/page");
        $this->assertEquals("some/wild/path/to/our/page", $this->builder->getPath());
    }

    public function testSetParameter()
    {
        $this->builder->setParameter("key", "value");
        $this->assertEquals("sale=true&columns=4&key=value", $this->builder->getQuery());
    }

    public function testSetParameters()
    {
        $this->builder->setParameters(array(
            "key" => "value",
            "height" => "20in",
            "colour" => "blue"
        ));
        $this->assertEquals("key=value&height=20in&colour=blue", $this->builder->getQuery());
    }

    public function testSetUser()
    {
        $this->builder->setPassword(null);
        $this->assertEquals("guest", $this->builder->getUserInfo());
    }

    public function testSetUserAndPassword()
    {
        $this->assertEquals("guest:password", $this->builder->getUserInfo());
    }

    public function testPasswordNoUser()
    {
        $this->builder->setUser(null);
        $this->assertEquals("", $this->builder->getUserInfo());
    }

    public function testHostAndPort()
    {
        $this->builder->setScheme(null)
            ->setUser(null)
            ->setPassword(null)
            ->setPath(null)
            ->setParameters(array())
            ->setFragment(null);
        $this->assertEquals("example.com:8080", $this->builder->build());
    }

    public function testSchemeHostAndPort()
    {
        $this->builder->setUser(null)
            ->setPassword(null)
            ->setPath(null)
            ->setParameters(array())
            ->setFragment(null);
        $this->assertEquals("http://example.com:8080", $this->builder->build());
    }

    public function testSchemeHostPortAndPath()
    {
        $this->builder->setUser(null)
            ->setPassword(null)
            ->setParameters(array())
            ->setFragment(null);
        $this->assertEquals("http://example.com:8080/some/wild/path", $this->builder->build());
    }

    public function testSchemeHostPortPathAndQuery()
    {
        $this->builder->setUser(null)
            ->setPassword(null)
            ->setFragment(null);
        $this->assertEquals("http://example.com:8080/some/wild/path?sale=true&columns=4", $this->builder->build());
    }

    public function testSchemeHostPortPathQueryAndFragment()
    {
        $this->builder->setUser(null)
            ->setPassword(null);
        $this->assertEquals("http://example.com:8080/some/wild/path?sale=true&columns=4#bookmark",
            $this->builder->build());
    }

    public function testSchemeUserHostPortPathQueryAndFragment()
    {
        $this->builder->setPassword(null);
        $this->assertEquals("http://guest@example.com:8080/some/wild/path?sale=true&columns=4#bookmark",
            $this->builder->build());
    }

    public function testFullUri()
    {
        $this->assertEquals("http://guest:password@example.com:8080/some/wild/path?sale=true&columns=4#bookmark",
            $this->builder->build());
    }

    public function testSchemeFromString()
    {
        $builder = new UriBuilder();
        $builder->setFromString("http://therest");
        $this->assertEquals("http", $builder->getScheme());
    }

    public function testUserFromString()
    {
        $builder = new UriBuilder();
        $builder->setFromString("guest@therest");
        $this->assertEquals("guest", $builder->getUser());
    }

    public function testUserAndPasswordFromString()
    {
        $builder = new UriBuilder();
        $builder->setFromString("guest:pass@therest");
        $this->assertEquals("guest", $builder->getUser());
        $this->assertEquals("pass", $builder->getPassword());
    }

    public function testHostFromString()
    {
        $builder = new UriBuilder();
        $builder->setFromString("localhost");
        $this->assertEquals("localhost", $builder->getHost());
    }

    public function testHostAndPortFromString()
    {
        $builder = new UriBuilder();
        $builder->setFromString("hans:gr00ber@localhost:4040");
        $this->assertEquals("localhost", $builder->getHost());
        $this->assertEquals(4040, $builder->getPort());
    }

    public function testPathFromString()
    {
        $builder = new UriBuilder();
        $builder->setFromString("localhost/path/to/somewhere");
        $this->assertEquals("/path/to/somewhere", $builder->getPath());
    }

    public function testQueryParamsFromString()
    {
        $builder = new UriBuilder();
        $builder->setFromString("localhost?key=val&up=down&right=left");
        $this->assertEquals(array(
            "key" => "val",
            "up" => "down",
            "right" => "left"
        ), $builder->getParameters());
    }

    public function testFragmentFromString()
    {
        $builder = new UriBuilder();
        $builder->setFromString("localhost/path#bookmark");
        $this->assertEquals("bookmark", $builder->getFragment());
    }

    public function testFullUriFromString()
    {
        $builder = new UriBuilder();
        $builder->setFromString("http://someone:somehow@somewhere:999/with/a/path?that=leads&no=where#tagged");
        $this->assertEquals("http", $builder->getScheme());
        $this->assertEquals("someone", $builder->getUser());
        $this->assertEquals("somehow", $builder->getPassword());
        $this->assertEquals("somewhere", $builder->getHost());
        $this->assertEquals(999, $builder->getPort());
        $this->assertEquals("/with/a/path", $builder->getPath());
        $this->assertEquals(array(
            "that" => "leads",
            "no" => "where"
        ), $builder->getParameters());
        $this->assertEquals("tagged", $builder->getFragment());
    }
}

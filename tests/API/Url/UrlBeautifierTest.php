<?php

use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Url\Beauty;
use GroupByInc\API\Url\UrlBeautifier;

class UrlBeautifierTest extends PHPUnit_Framework_TestCase
{
    const DEFAULT_BEAUTIFIER = "default";

    /** @var UrlBeautifier */
    private $beautifier;

    public function setUp()
    {
        UrlBeautifier::createUrlBeautifier(self::DEFAULT_BEAUTIFIER);
        $this->beautifier = $this->getBeautifier(self::DEFAULT_BEAUTIFIER);
        $this->beautifier->clearSavedFields();
    }

    private function getBeautifier($identifier)
    {
        $beautifiers = UrlBeautifier::getUrlBeautifiers();
        return $beautifiers[$identifier];
    }

    public function testStopVowels()
    {
        try {
            $this->beautifier->addRefinementMapping("u", "test");
            $this->fail("Should throw exception for vowels");
        } catch (RuntimeException $e) {
            // expected
        }

        try {
            $this->beautifier->setSearchMapping("e");
            $this->fail("Should throw exception for vowels");
        } catch (RuntimeException $e) {
            // expected
        }
    }

    public function testMultipleBeautifiers()
    {
        $identifier = "default2";
        UrlBeautifier::createUrlBeautifier($identifier);

        $beautifier2 = $this->getBeautifier($identifier);
        $this->beautifier->addRefinementMapping("t", "test");
        $this->assertEquals("/value/t", $this->beautifier->toUrl(null, "test=value"));
        $this->assertEquals("?refinements=%7Etest%3Dvalue", $beautifier2->toUrl(null, "test=value"));
    }

    public function testQueryUrl()
    {
        $this->beautifier->setSearchMapping("q");
        $url = $this->beautifier->toUrl("this is a test", null);
        $this->assertEquals("/this+is+a+test/q", $url);
    }

    public function testRefinementsUrl()
    {
        $this->beautifier->addRefinementMapping("t", "test");
        $url = $this->beautifier->toUrl("", "test=value");
        $this->assertEquals("/value/t", $url);
    }

    public function testMultipleRefinements()
    {
        $this->setUpTestHeightAndCategoryRefinements();
        $url = $this->beautifier->toUrl("", "test=value~height=20in~category=computer accessories");
        $this->assertEquals("/value/20in/computer+accessories/thc", $url);
    }

    private function setUpTestHeightAndCategoryRefinements()
    {
        $this->beautifier->addRefinementMapping("t", "test")
            ->addRefinementMapping("h", "height")
            ->addRefinementMapping("c", "category");
    }

    public function testFullSearchUrl()
    {
        $this->beautifier->setSearchMapping("q")
            ->addRefinementMapping("t", "test");
        $url = $this->beautifier->toUrl("this is a test", "test=value");
        $this->assertEquals("/this+is+a+test/value/qt", $url);
    }

    public function testDetailQuery()
    {
        $query = $this->beautifier->fromUrl("http://example.com/details?p=4&id=243478931&b=test");
        $this->assertEquals("~id=243478931", $query->getRefinementString());
    }

    public function testSearchQuery()
    {
        $this->beautifier->setSearchMapping("q")
            ->addRefinementMapping("t", "test");
        $query = $this->beautifier->fromUrl("http://example.com/this%20is%20a%20test/value/qt");
        $this->assertEquals("this is a test", $query->getQuery());
        $this->assertEquals("~test=value", $query->getRefinementString());
    }

    public function testInvalidReferenceBlock()
    {
        $query = $this->beautifier->fromUrl("http://example.com/this%20is%20a%20test/value/qtrs", null);
        $this->assertEquals(null, $query);
    }

    public function testRange()
    {
        $refinement = "test=bob~price:10..20";
        $expectedUrl = "/bob/t?refinements=%7Eprice%3A10..20";

        $this->beautifier->addRefinementMapping("t", "test");
        $this->assertEquals($expectedUrl, $this->beautifier->toUrl(null, $refinement));
        $this->beautifier->addRefinementMapping("p", "price");
        $actual = $this->beautifier->toUrl(null, $refinement);
        $this->assertEquals($expectedUrl, $actual);
    }

    public function testDeepSearchQuery()
    {
        $this->beautifier->setSearchMapping("q")
            ->addRefinementMapping("t", "test");
        $query = $this->beautifier->fromUrl("http://example.com/path/to/search/this%20is%20a%20test/value/qt");

        $this->assertEquals("this is a test", $query->getQuery());
        $this->assertEquals("~test=value", $query->getRefinementString());
    }

    public function testSearchUrlBackAndForth()
    {
        $url = "/this%20is%20a%20test/value/qt";
        $this->beautifier->setSearchMapping("q")
            ->addRefinementMapping("t", "test");

        $query = $this->beautifier->fromUrl($url);
        $this->assertEquals("this is a test", $query->getQuery());

        /** @var Navigation[] $navigations */
        $navigations = array_values($query->getNavigations());
        $this->assertEquals("test", $navigations[0]->getName());
        /** @var SelectedRefinementValue $valueRefinement */
        $valueRefinement = $navigations[0]->getRefinements()[0];
        $this->assertEquals("value", $valueRefinement->getValue());
    }

    public function testExistingMapping()
    {
        try {
            $this->beautifier->setSearchMapping("q")
                ->addRefinementMapping("q", "quasorLightLevel");
            $this->fail("should throw exception");
        } catch (RuntimeException $e) {
            // expected
            $this->assertEquals("This token: q is already mapped to: search", $e->getMessage());
        }
    }

    public function testEmptyQueryString()
    {
        $this->beautifier->fromUrl("");
    }

    public function testAddSameRefinementMultipleTimes()
    {
        $this->setUpTestHeightAndCategoryRefinements();
        $this->beautifier->setAppend(".html");
        $url = $this->beautifier->toUrl("",
            "test=value~test=value~test=value2~height=20in~category=computer+accessories");
        $this->assertEquals("/value/value2/20in/computer%2Baccessories/tthc.html", $url);
    }

    public function testAppend()
    {
        $this->beautifier->setAppend(".html");
        $this->setUpTestHeightAndCategoryRefinements();
        $url = $this->beautifier->toUrl("", "test=value~height=20in~category=computer accessories");
        $this->assertEquals("/value/20in/computer+accessories/thc.html", $url);
    }

    public function testAppendWithSlash()
    {
        $this->beautifier->setAppend("/index.html");
        $this->setUpTestHeightAndCategoryRefinements();
        $url = $this->beautifier->toUrl("", "test=value~height=20in~category=computer accessories");
        $this->assertEquals("/value/20in/computer+accessories/thc/index.html", $url);
    }

    public function testUnappend()
    {
        $this->beautifier->setAppend(".html")
            ->addRefinementMapping("t", "test")
            ->addRefinementMapping("h", "height");
        $query = $this->beautifier->fromUrl("/value/20in/th.html");

        $navigations = array_values($query->getNavigations());
        $this->assertEquals(2, count($navigations));
        $this->assertNavigation("test", "=value", $navigations[0]);
        $this->assertNavigation("height", "=20in", $navigations[1]);
    }

    public function assertNavigation($expectedNavName, $expectedValue, Navigation $navigation)
    {
        $this->assertEquals($expectedNavName, $navigation->getName());
        $this->assertEquals($expectedValue, $navigation->getRefinements()[0]->toTildeString());
    }

    public function testUnappendWithSlash()
    {
        $this->beautifier->setAppend("/index.html")
            ->addRefinementMapping("t", "test")
            ->addRefinementMapping("h", "height");
        $query = $this->beautifier->fromUrl("/value/20in/th/index.html");

        $navigations = array_values($query->getNavigations());
        $this->assertEquals(2, count($navigations));
        $this->assertNavigation("test", "=value", $navigations[0]);
        $this->assertNavigation("height", "=20in", $navigations[1]);
    }

    public function testUnmappedToUrl()
    {
        $this->setUpTestHeightAndCategoryRefinements();
        $url = $this->beautifier->toUrl("",
            "test=value~height=20in~category2=mice~cat3=wireless mice~category=computer accessories");
        $this->assertEquals(
            "/value/20in/computer+accessories/thc?refinements=%7Ecategory2%3Dmice%7Ecat3%3Dwireless+mice", $url);
    }

    public function testUnmappedToUrlWithModifiedName()
    {
        $this->setUpTestHeightAndCategoryRefinements();
        $this->beautifier->setRefinementsQueryParamName("r");
        $url = $this->beautifier->toUrl("",
            "test=value~height=20in~category2=mice~cat3=wireless mice~category=computer accessories");
        $this->assertEquals("/value/20in/computer+accessories/thc?r=%7Ecategory2%3Dmice%7Ecat3%3Dwireless+mice", $url);
    }

    public function testUnmappedFromUrl()
    {
        $this->setUpTestHeightAndCategoryRefinements();
        $query = $this->beautifier->fromUrl(
            "/value/20in/computer%20accessories/thc?refinements=category2%3Dmice%7Ecat3%3Dwireless%20mice");

        $navigations = array_values($query->getNavigations());
        $this->assertEquals(5, count($navigations));
        $this->assertNavigation("test", "=value", $navigations[0]);
        $this->assertNavigation("height", "=20in", $navigations[1]);
        $this->assertNavigation("category", "=computer accessories", $navigations[2]);
        $this->assertNavigation("category2", "=mice", $navigations[3]);
        $this->assertNavigation("cat3", "=wireless mice", $navigations[4]);
    }

    public function testCanonical()
    {
        $this->setUpTestHeightAndCategoryRefinements();
        $this->assertEquals($this->beautifier->toUrl(null,
            "~height=20in~category2=mice~cat3=wireless mice~test=value~category=computer accessories"),
            $this->beautifier->toUrl(null,
                "~height=20in~category=computer accessories~test=value~category2=mice~cat3=wireless mice"));
    }

    public function testSearchWithSlash()
    {
        $this->beautifier->setSearchMapping("q");
        $this->assertEquals("/photo%2Fcommodity/q", $this->beautifier->toUrl("photo/commodity", null));
    }

    public function testRefinementWithSlash()
    {
        $this->beautifier->addRefinementMapping("t", "test");
        $this->assertEquals("/photo%2Fcommodity/t", $this->beautifier->toUrl(null, "test=photo/commodity"));
    }

    public function testUnencodePlus()
    {
        $this->beautifier->setSearchMapping("q")
            ->addRefinementMapping("d", "department")
            ->setAppend("/index.html");
        $this->assertFromUrl("/aoeu/laptop/MAGNOLIA+HOME+THEATRE/qd/index.html", null, "MAGNOLIA HOME THEATRE");
    }

    private function assertFromUrl($url, $searchString)
    {
        $query = $this->beautifier->fromUrl($url);
        if (!empty($searchString)) {
            $this->assertEquals($searchString, $query->getQuery());
        }
        if (func_num_args() > 2) {
            /** @var string[] $expectedRefinements */
            $expectedRefinements = array_slice(func_get_args(), 2);
            /** @var Navigation[] $navigations */
            $navigations = array_values($query->getNavigations());
            for ($i = 0; $i < count($expectedRefinements); $i++) {
                /** @var SelectedRefinementValue $valueRefinement */
                $valueRefinement = $navigations[$i]->getRefinements()[0];
                $this->assertEquals($expectedRefinements[$i], $valueRefinement->getValue());
            }
        }
    }

    public function assertFromUrlWithSlash()
    {
        $this->beautifier->setSearchMapping("q")
            ->addRefinementMapping("d", "department")
            ->setAppend("/index.html");
        $this->assertFromUrl("/taylor/PHOTO%252FCOMMODITIES/qd/index.html", null, "PHOTO/COMMODITIES");
    }

    public function assertFromUrlWithOneReplace()
    {
        $this->setSearchAndIndex();
        $this->beautifier->addReplacementRule("&", " ");
        $this->assertToAndFromUrl("black&decker", null);
    }

    private function setSearchAndIndex()
    {
        $this->beautifier->setSearchMapping("q")
            ->setAppend("/index.html");
    }

    private function assertToAndFromUrl($searchString, $refinementString)
    {
        $url = $this->beautifier->toUrl($searchString, $refinementString);
        $query = $this->beautifier->fromUrl($url);
        $this->assertEquals($searchString, $query->getQuery());
        if (func_num_args() > 2) {
            $expectedRefinements = array_slice(func_get_args(), 2);
            /** @var Navigation[] $navigations */
            $navigations = array_values($query->getNavigations());
            for ($i = 0; $i < count($expectedRefinements); $i++) {
                /** @var SelectedRefinementValue $valueRefinement */
                $valueRefinement = $navigations[$i]->getRefinements()[0];
                $this->assertEquals($expectedRefinements[$i], $valueRefinement->getValue());
            }
        }
    }

    public function assertFromUrlWithMultipleReplace()
    {
        $this->setSearchAndIndex();
        $this->beautifier->addReplacementRule("&", " ")
            ->addReplacementRule("B", "b")
            ->addReplacementRule("D", "d");
        $this->assertToAndFromUrl("Black&Decker", null);
    }

    public function assertFromUrlWithOneInsert()
    {
        $this->setSearchAndIndex();
        $this->assertFromUrl("/black+decker/q/index.html?z=i1-1", "1black decker");
    }

    public function assertFromUrlWithReplaceAndInsertionsOrderMatters()
    {
        $this->setSearchAndIndex();
        $this->beautifier->addReplacementRule("d", "D")
            ->addReplacementRule("1", null)
            ->addReplacementRule("2", null)
            ->addReplacementRule("3", null)
            ->addReplacementRule("&", " ")
            ->addReplacementRule("b", "B");
        $searchString = "123black&decker";
        $expected = "/Black+Decker/q";
        $this->assertEquals($expected, substr($this->beautifier->toUrl($searchString, null), 0, strlen($expected)));
        $this->assertToAndFromUrl($searchString, null);
    }

    public function assertFromUrlBadReplace()
    {
        $this->setSearchAndIndex();
        $this->assertFailingQuery("/black+decker/q/index.html?z=2-B--");
    }

    private function assertFailingQuery($uri)
    {
        try {
            $this->beautifier->fromUrl($uri);
            $this->fail("Expected an exception");
        } catch (RuntimeException $e) {
            // expected
        }
    }

    public function assertFromUrlBadInsert()
    {
        $this->setSearchAndIndex();
        $this->assertFailingQuery("/black+decker/q/index.html?z=c2-B");
    }

    public function assertFromUrlBadInsert2()
    {
        $this->setSearchAndIndex();
        $this->assertFailingQuery("/black+decker/q/index.html?z=ii2-B");
    }

    public function assertFromUrlReplaceBadIndex()
    {
        $this->setSearchAndIndex();
        $this->assertFromUrl("/black+decker/q/index.html?z=26-R", "black decker");
    }

    public function assertFromUrlReplaceBadReplacementString()
    {
        $this->setSearchAndIndex();
        $this->assertFailingQuery("/black+decker/q/index.html?z=-1-R");
    }

    public function assertFromUrlReplaceBadIndex3()
    {
        $this->setSearchAndIndex();
        $this->assertFromUrl("/black+decker/q/index.html?z=0-R", "black decker");
    }

    public function assertFromUrlReplaceBadIndex4()
    {
        $this->setSearchAndIndex();
        $this->assertFromUrl("/black+decker/q/index.html?z=13-R", "black decker");
    }

    public function assertFromUrlReplaceNoIndex()
    {
        $this->setSearchAndIndex();
        $this->assertFailingQuery("/black+decker/q/index.html?z=-R");
    }

    public function assertFromUrlReplaceValidEdgeIndex()
    {
        $this->setSearchAndIndex();
        $this->assertFromUrl("/black+decker/q/index.html?z=12-R", "black deckeR");
    }

    public function assertFromUrlInsertBadIndex()
    {
        $this->setSearchAndIndex();
        $this->assertFromUrl("/black+decker/q/index.html?z=i26-R", "black decker");
    }

    public function assertFromUrlInsertMalformedIndex()
    {
        $this->setSearchAndIndex();
        $this->assertFailingQuery("/black+decker/q/index.html?z=i-1-R");
    }

    public function assertFromUrlInsertBadIndex3()
    {
        $this->setSearchAndIndex();
        $this->assertFromUrl("/black+decker/q/index.html?z=i0-R", "black decker");
    }

    public function assertFromUrlInsertNoIndex()
    {
        $this->setSearchAndIndex();
        $this->assertFailingQuery("/black+decker/q/index.html?z=i-R");
    }

    public function assertFromUrlInsertValidEdgeIndex()
    {
        $this->setSearchAndIndex();
        $this->assertFromUrl("/black+decker/q/index.html?z=i13-R-6-%26", "black&deckerR");
    }

    public function assertFromUrlWithReplace()
    {
        $this->beautifier->setSearchMapping("q")
            ->addRefinementMapping("d", "department")
            ->addRefinementMapping("c", "category")
            ->setAppend("/index.html");
        $this->assertFromUrl("/mice/wireless/dell/cdq/index.html?z=1-M-i14-123-18-D", "Dell", "Mice", "wireless123");
    }

    public function assertFromUrlWithReplaceFullUrl()
    {
        $this->beautifier->setSearchMapping("q")
            ->addRefinementMapping("d", "department")
            ->addRefinementMapping("c", "category")
            ->setAppend("/index.html");
        $this->assertFromUrl("www.example.com/mice/wireless/dell/cdq/index.html?z=1-M-i14-123-18-D", "Dell", "Mice",
            "wireless123");
    }

    public function testSimpleToUrlOneReplace()
    {
        $this->beautifier->setSearchMapping("q")
            ->addReplacementRule("/", "-");
        $searchString = "this is/a test";
        $this->assertEquals("/this+is-a+test/q?z=8-%2F", $this->beautifier->toUrl($searchString, null));
        $this->assertToAndFromUrl($searchString, null);
    }

    public function testSimpleToUrlMultipleReplace()
    {
        $this->beautifier->setSearchMapping("q")
            ->addReplacementRule("/", "-")
            ->addReplacementRule("T", "t");
        $searchString = "This is/a Test";
        $this->assertEquals("/this+is-a+test/q?z=8-%2F-1-T-11-T", $this->beautifier->toUrl($searchString, null));
        $this->assertToAndFromUrl($searchString, null);
    }

    public function testSimpleToUrlReplaceWithEmpty()
    {
        $this->beautifier->setSearchMapping("q")
            ->addReplacementRule("/", null);
        $searchString = "this is/a test";
        $this->assertEquals("/this+isa+test/q?z=i8-%2F", $this->beautifier->toUrl($searchString, null));
        $this->assertToAndFromUrl($searchString, null);
    }

    public function testSimpleToUrlMultipleReplaceWithEmpty()
    {
        $this->beautifier->setSearchMapping("q")
            ->addReplacementRule("/", null)
            ->addReplacementRule("_", null);
        $searchString = "this _is/a _test";
        $this->assertEquals("/this+isa+test/q?z=i9-%2F-i6-_-i10-_", $this->beautifier->toUrl($searchString, null));
        $this->assertToAndFromUrl($searchString, null);
    }

    public function testSimpleToUrlMultipleReplaceOrderMatters()
    {
        $this->beautifier->setSearchMapping("q")
            ->addReplacementRule("a", null)
            ->addReplacementRule("/", "-")
            ->addReplacementRule("_", null);
        $this->assertToAndFromUrl("this _is/a _test", null);
    }

    public function testToUrlWithReplace()
    {
        $this->setUpTestHeightAndCategoryRefinements();
        $this->beautifier->setSearchMapping("q")
            ->addReplacementRule("/", "-")
            ->addReplacementRule("&", null);
        $searchString = "test&query";
        $refinements = "test=value~height=20/in~category=computer accessories";
        $url = $this->beautifier->toUrl($searchString, $refinements);
        $this->assertEquals("/value/20-in/computer+accessories/testquery/thcq?z=9-%2F-i38-%26", $url);
        $this->assertToAndFromUrl($searchString, $refinements, "value", "20/in", "computer accessories");
    }

    public function testToUrlWithReplaceDash()
    {
        $this->setUpTestHeightAndCategoryRefinements();
        $this->beautifier->setSearchMapping("q")
            ->addReplacementRule("-", " ")
            ->addReplacementRule("&", null);
        $searchString = "test&query";
        $refinements = "test=value~height=20-in~category=computer accessories";
        $url = $this->beautifier->toUrl($searchString, $refinements);
        $this->assertEquals("/value/20+in/computer+accessories/testquery/thcq?z=9---i38-%26", $url);
        $this->assertToAndFromUrl($searchString, $refinements, "value", "20-in", "computer accessories");
    }

    public function testToUrlWithReplaceWithRefinement()
    {
        $this->setUpTestHeightCategoryAndSearch();
        $this->beautifier->addReplacementRule("/", " ")
            ->addReplacementRule("&", " ", Beauty::SEARCH_NAVIGATION_NAME);
        $refinements = "test=val&ue~height=20/in~category=computer accessories";
        $this->assertToAndFromUrl("test&query", $refinements, "val&ue", "20/in", "computer accessories");
    }

    private function setUpTestHeightCategoryAndSearch()
    {
        $this->beautifier->setSearchMapping("q");
        $this->setUpTestHeightAndCategoryRefinements();
    }

    public function testToUrlWithUnmappedRefinements()
    {
        $this->beautifier
            ->addRefinementMapping("h", "height")
            ->addRefinementMapping("c", "category")
            ->setSearchMapping("q")
            ->addReplacementRule("-", " ")
            ->addReplacementRule("&", null);
        $searchString = "test&query";
        $refinements = "test=value~height=20-in~category=computer accessories";
        $url = $this->beautifier->toUrl($searchString, $refinements);
        $this->assertEquals("/20+in/computer+accessories/testquery/hcq?z=3---i32-%26&refinements=%7Etest%3Dvalue",
            $url);
        $this->assertToAndFromUrl($searchString, $refinements, "20-in", "computer accessories", "value");
    }

    public function assertToAndFromUrlWithRefinementSpecificReplacements()
    {
        $this->setUpTestHeightCategoryAndSearch();
        $this->beautifier->setAppend("/index.html")
            ->addReplacementRule("&", " ", Beauty::SEARCH_NAVIGATION_NAME);
        $searchString = "test&query";
        $refinements = "test=val&ue~height=20-in~category=computer accessories";
        $url = $this->beautifier->toUrl($searchString, $refinements);
        $expected = "/test+query/val%2526ue";
        $this->assertEquals($expected, substr($url, 0, strlen($expected)));
        $this->assertToAndFromUrl($searchString, $refinements, "val&ue", "20-in", "computer accessories");
    }

    public function assertToAndFromUrlWithMultipleRefinementSpecificReplacements()
    {
        $this->setUpTestHeightCategoryAndSearch();
        $this->beautifier->setAppend("/index.html")
            ->addReplacementRule("&", " ", Beauty::SEARCH_NAVIGATION_NAME)
            ->addReplacementRule("i", "m", "height")
            ->addReplacementRule("e", "a", "category");
        $searchString = "test&query";
        $refinements = "test=val&ue~height=20-in~category=computer accessories";
        $url = $this->beautifier->toUrl($searchString, $refinements);
        $expected = "/test+query/val%2526ue/20-mn/computar+accassorias";
        $this->assertEquals($expected, substr($url, 0, strlen($expected)));
        $this->assertToAndFromUrl($searchString, $refinements, "val&ue", "20-in", "computer accessories");
    }

    public function assertToAndFromUrlWithReplaceWithSpecialChar()
    {
        $this->setUpTestHeightCategoryAndSearch();
        $this->beautifier->setAppend("/index.html")
            ->addReplacementRule("e", "/")
            ->addReplacementRule("a", "\\");
        $refinements = "test=val&ue~height=20-in~category=computer accessories";
        $this->assertToAndFromUrl("test&query", $refinements, "val&ue", "20-in", "computer accessories");
    }

    public function assertToAndFromUrlWithReplaceWithSpecialChar2()
    {
        $this->setUpTestHeightCategoryAndSearch();
        $this->beautifier->setAppend("/index.html")
            ->addReplacementRule("e", "%");
        $refinements = "test=val&ue~height=20-in~category=computer accessories";
        $this->assertToAndFromUrl("test&qu%ery", $refinements, "val&ue", "20-in", "computer accessories");
    }

    public function assertToAndFromUrlWithReplaceWithRegexSpecialChar()
    {
        $this->setUpTestHeightCategoryAndSearch();
        $this->beautifier->setAppend("/index.html")
            ->addReplacementRule(".", "%");
        $refinements = "test=val&ue~height=20-in~category=computer accessories";
        $this->assertToAndFromUrl("test&qu%ery", $refinements, "val&ue", "20-in", "computer accessories");
    }

    public function assertToAndFromUrlWithReplaceWithRegexSpecialChar2()
    {
        $this->setUpTestHeightCategoryAndSearch();
        $this->beautifier->setAppend("/index.html")
            ->addReplacementRule("e", ".");
        $refinements = "test=val&ue~height=20-in~category=computer accessories";
        $this->assertToAndFromUrl("test&qu%ery", $refinements, "val&ue", "20-in", "computer accessories");
    }

    public function assertToAndFromUrlWithReplaceWithSameChar()
    {
        $this->setUpTestHeightCategoryAndSearch();
        $this->beautifier->setAppend("/index.html")
            ->addReplacementRule("e", "e");
        $refinements = "test=val&ue~height=20-in~category=computer accessories";
        $this->assertToAndFromUrl("test&qu%ery", $refinements, "val&ue", "20-in", "computer accessories");
    }

    public function assertToAndFromUrlWithNullSearchString()
    {
        $this->setUpTestHeightCategoryAndSearch();
        $this->beautifier->setAppend("/index.html")
            ->addReplacementRule("e", "e");
        $refinements = "test=val&ue~height=20-in~category=computer accessories";
        $this->assertToAndFromUrl(null, $refinements, "val&ue", "20-in", "computer accessories");
    }
}

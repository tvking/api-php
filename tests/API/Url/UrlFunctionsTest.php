<?php
use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\RefinementRange;
use GroupByInc\API\Model\RefinementValue;
use GroupByInc\API\Url\UrlBeautifier;
use GroupByInc\API\Url\UrlFunctions;

class UrlFunctionsTest extends PHPUnit_Framework_TestCase
{

    const DEFAULT_BEAUTIFIER = "default";
    const DEFAULT_NAVIGATION = "default";
    const HEIGHT_NAVIGATION = "height";
    const CATEGORY_NAVIGATION = "category";

    /** @var UrlBeautifier $beautifier */
    private $beautifier;

    public function setUp()
    {
        UrlBeautifier::createUrlBeautifier(self::DEFAULT_BEAUTIFIER);
        $this->beautifier = UrlBeautifier::getUrlBeautifiers()[self::DEFAULT_BEAUTIFIER];
        $this->beautifier->addRefinementMapping('h', 'height')
            ->addRefinementMapping('c', 'category')
            ->setSearchMapping('q');
    }

    public function testAddToUrl()
    {
        $navigations = [];
        $refinement = new RefinementValue();
        $refinement->setValue('3.2m');
        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, 'toast', $navigations, self::HEIGHT_NAVIGATION, $refinement);
        $this->assertEquals('/3.2m/toast/hq', $url);
        $this->assertEquals(1, count($navigations));

        $refinement = new RefinementValue();
        $refinement->setValue('toys');
        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, 'toast', $navigations, self::CATEGORY_NAVIGATION, $refinement);
        $this->assertEquals('/3.2m/toys/toast/hcq', $url);
        $this->assertEquals(2, count($navigations));
    }

    public function testAddRangeToUrl()
    {
        $navigations = [];
        $refinement = new RefinementRange();
        $refinement->setLow('12')
            ->setHigh('34');
        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, 'toast', $navigations, self::CATEGORY_NAVIGATION, $refinement);
        $this->assertEquals('/toast/q?refinements=%7Ecategory%3A12..34', $url);
    }
    public function testRemoveFromUrl()
    {
        $navigations = [];
        $refinement1 = new RefinementValue();
        $refinement1->setValue('3.2m');
        $refinement2 = new RefinementValue();
        $refinement2->setValue('toys');
        UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, 'toast', $navigations, self::HEIGHT_NAVIGATION, $refinement1);
        UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, 'toast', $navigations, self::CATEGORY_NAVIGATION, $refinement2);

        $url = UrlFunctions::toUrlRemove(self::DEFAULT_BEAUTIFIER, 'toast', $navigations, self::CATEGORY_NAVIGATION, $refinement2);
        $this->assertEquals('/3.2m/toast/hq', $url);
        $this->assertEquals(1, count($navigations));
    }

}

<?php
use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\SelectedRefinementRange;
use GroupByInc\API\Model\SelectedRefinementValue;
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
            ->setSearchMapping('q');
    }

    public function testAddToUrl()
    {
        $navigations = [];
        $this->beautifier->addRefinementMapping('c', 'category');
        $refinement = new SelectedRefinementValue();
        $refinement->setValue('3.2m');
        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, 'toast', $navigations, self::HEIGHT_NAVIGATION, $refinement);
        $this->assertEquals('/3.2m/toast/hq', $url);

        $nav1 = new Navigation();
        $nav1->setName(self::HEIGHT_NAVIGATION)->setDisplayName("Height")
            ->setType(Navigation\Type::String)
            ->setRefinements([$refinement]);

        array_push($navigations, $nav1);

        $refinement = new SelectedRefinementValue();
        $refinement->setValue('toys');
        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, 'toast', $navigations, self::CATEGORY_NAVIGATION, $refinement);
        $this->assertEquals('/3.2m/toast/toys/hqc', $url);
        $this->assertEquals(1, count($navigations));
    }

    public function testAddRangeToUrl()
    {
        $navigations = [];
        $this->beautifier->addRefinementMapping('c', 'category');
        $refinement = new SelectedRefinementRange();
        $refinement->setLow('12')
            ->setHigh('34');
        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, 'toast', $navigations, self::CATEGORY_NAVIGATION, $refinement);
        $this->assertEquals('/toast/q?refinements=%7Ecategory%3A12..34', $url);
    }
    public function testRemoveFromUrl()
    {
        $navigations = [];
        $this->beautifier->addRefinementMapping('c', 'category');
        $refinement1 = new SelectedRefinementValue();
        $refinement1->setValue('3.2m');

        $nav1 = new Navigation();
        $nav1->setName(self::HEIGHT_NAVIGATION)->setDisplayName("Height")
            ->setType(Navigation\Type::String)
            ->setRefinements([$refinement1]);

        array_push($navigations, $nav1);

        $this->assertEquals(1, count($navigations));
        $url = UrlFunctions::toUrlRemove(self::DEFAULT_BEAUTIFIER, 'toast', $navigations, self::HEIGHT_NAVIGATION, $refinement1);
        $this->assertEquals('/toast/q', $url);
        $this->assertEquals(0, count($navigations));
    }

    public function testRefinementAdditionWithMapping() {
        $navigations = [];

        $this->beautifier->addRefinementMapping('g', "gender");
        $this->beautifier->addRefinementMapping('t', "product");
        $this->beautifier->addRefinementMapping('s', "primarysport");
        $this->beautifier->addRefinementMapping('c', "simpleColorDesc");
        $this->beautifier->addRefinementMapping('l', "collections");
        $this->beautifier->addRefinementMapping('f', "league");
        $this->beautifier->setAppend("/index.html");

        $refinement1 = new SelectedRefinementValue();
        $refinement1->setValue("Women");

        $nav1 = new Navigation();
        $nav1->setName("gender")->setDisplayName("Gender")
            ->setType(Navigation\Type::String)
            ->setRefinements([$refinement1]);

        $refinement2 = new SelectedRefinementValue();
        $refinement2->setValue("Pink");

        $nav2 = new Navigation();
        $nav2->setName("simpleColorDesc")->setDisplayName("Color")
            ->setType(Navigation\Type::String)
            ->setRefinements([$refinement2]);

        array_push($navigations, $nav1, $nav2);


        $refinement3 = new SelectedRefinementValue();
        $refinement3->setValue("Clothing");

        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, '', $navigations, 'product', $refinement3);
        $this->assertEquals("/Women/Clothing/Pink/gtc/index.html", $url);
    }

    public function testRefinementAdditionWithoutMapping() {
        $navigations = [];

        $this->beautifier->setAppend("/index.html");

        $refinement1 = new SelectedRefinementValue();
        $refinement1->setValue("Women");

        $nav1 = new Navigation();
        $nav1->setName("gender")->setDisplayName("Gender")
            ->setType(Navigation\Type::String)
            ->setRefinements([$refinement1]);

        $refinement2 = new SelectedRefinementValue();
        $refinement2->setValue("Pink");

        $nav2 = new Navigation();
        $nav2->setName("simpleColorDesc")->setDisplayName("Color")
            ->setType(Navigation\Type::String)
            ->setRefinements([$refinement2]);

        array_push($navigations, $nav1, $nav2);

        $refinement3 = new SelectedRefinementValue();
        $refinement3->setValue("Clothing");

        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, '', $navigations, 'product', $refinement3);
        $this->assertEquals("/index.html?refinements=%7Egender%3DWomen%7EsimpleColorDesc%3DPink%7Eproduct%3DClothing", $url);
    }

    public function testRefinementAdditionWithMappingMulti() {
        $navigations = [];

        $this->beautifier->addRefinementMapping('g', "gender");
        $this->beautifier->addRefinementMapping('t', "product");
        $this->beautifier->addRefinementMapping('s', "primarysport");
        $this->beautifier->addRefinementMapping('c', "simpleColorDesc");
        $this->beautifier->addRefinementMapping('l', "collections");
        $this->beautifier->addRefinementMapping('f', "league");
        $this->beautifier->setAppend("/index.html");

        $refinement1 = new SelectedRefinementValue();
        $refinement1->setValue("Women");

        $nav1 = new Navigation();
        $nav1->setName("gender")->setDisplayName("Gender")
            ->setType(Navigation\Type::String)
            ->setRefinements([$refinement1]);

        $refinement2 = new SelectedRefinementValue();
        $refinement2->setValue("Pink");

        $nav2 = new Navigation();
        $nav2->setName("simpleColorDesc")->setDisplayName("Color")
            ->setType(Navigation\Type::String)
            ->setRefinements([$refinement2]);

        array_push($navigations, $nav1, $nav2);


        $refinement3 = new SelectedRefinementValue();
        $refinement3->setValue("Men");

        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, '', $navigations, 'gender', $refinement3);
        $this->assertEquals("/Women/Men/Pink/ggc/index.html", $url);

        $refinement4 = new SelectedRefinementValue();
        $refinement4->setValue("Kid");


        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, '', $navigations, 'gender', $refinement4);
        $this->assertEquals("/Women/Kid/Pink/ggc/index.html", $url);
    }

    public function testRefinementAdditionWithCategoryExpansion () {
        $navigations = [];

        $this->beautifier->addRefinementMapping('s', "size");
        $this->beautifier->setAppend("/index.html");

        $refinement = new SelectedRefinementValue();
        $refinement->setValue("Category Root~Athletics~Men's~Sneakers");


        $url = UrlFunctions::toUrlAdd(self::DEFAULT_BEAUTIFIER, '', $navigations, 'category_leaf_expanded', $refinement);
        $this->assertEquals("/index.html?refinements=%7Ecategory_leaf_expanded%3DCategory+Root%7EAthletics%7EMen%27s%7ESneakers", $url);
    }

}
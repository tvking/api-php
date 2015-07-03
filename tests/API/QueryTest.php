<?php

require_once __DIR__ . '/JsonSerializeTest.php';

use GroupByInc\API\Query;
use GroupByInc\API\Request\Sort;
use Httpful\Response;

class QueryTest extends PHPUnit_Framework_TestCase
{
    private static $QUERY;
    private static $REFINEMENTS_QUERY;

    public static function init()
    {
        self::$QUERY = '{"clientKey":"XXXX-XXXX-XXXX-XXXX","collection":"testproducts","area":"Production",' .
            '"biasingProfile":"testProfile","language":"en","query":"the","sort":{"field":"price","order":"Descending"},' .
            '"fields":["brand","category","height"],"orFields":["price","color"],"refinements":[{"navigationName":"green",' .
            '"exclude":true,"high":"delicious","low":"atrocious","type":"Range"},{"navigationName":"green","exclude":false,' .
            '"value":"malaise","type":"Value"}],"customUrlParams":[{"key":"guava","value":"mango"}],"skip":20,"pageSize":14,' .
            '"disableAutocorrection":true,"pruneRefinements":false,"wildcardSearchEnabled":true}';

        self::$REFINEMENTS_QUERY = '{"originalQuery":' . self::$QUERY . ',"navigationName":"height"}';
    }

    public function testSerializeQuery()
    {
        $query = new Query();
        $query->setQuery('the');
        $query->setSort(JsonSerializeTest::$OBJ_SORT);
        $query->setSkip(20);
        $query->setPageSize(14);
        $query->setCollection('testproducts');
        $query->setArea('Production');
        $query->setBiasingProfile('testProfile');
        $query->setLanguage('en');
        $query->setPruneRefinements(false);
        $query->setDisableAutocorrection(true);
        $query->setCustomUrlParams(array(JsonSerializeTest::$OBJ_CUSTOM_URL_PARAM));
        $query->setNavigations(array(JsonSerializeTest::$OBJ_NAVIGATION));
        $query->addFields(array("brand", "category", "height"));
        $query->addOrFields(array("price", "color"));
        $query->setWildcardSearchEnabled(true);

        $json = $query->getBridgeJson('XXXX-XXXX-XXXX-XXXX');
        $this->assertEquals(self::$QUERY, $json);
    }

    public function testSerializeRefinementsQuery()
    {
        $query = new Query();
        $query->setQuery('the');
        $query->setSort(JsonSerializeTest::$OBJ_SORT);
        $query->setSkip(20);
        $query->setPageSize(14);
        $query->setCollection('testproducts');
        $query->setArea('Production');
        $query->setBiasingProfile('testProfile');
        $query->setLanguage('en');
        $query->setPruneRefinements(false);
        $query->setDisableAutocorrection(true);
        $query->setCustomUrlParams(array(JsonSerializeTest::$OBJ_CUSTOM_URL_PARAM));
        $query->setNavigations(array(JsonSerializeTest::$OBJ_NAVIGATION));
        $query->addFields(array("brand", "category", "height"));
        $query->addOrFields(array("price", "color"));
        $query->setWildcardSearchEnabled(true);

        $json = $query->getBridgeRefinementsJson('XXXX-XXXX-XXXX-XXXX', 'height');
        $this->assertEquals(self::$REFINEMENTS_QUERY, $json);
    }


    public function testSplitRange()
    {
        $query = new Query();
        $split = $query->splitRefinements("test=bob~price:10..20");
        $this->assertEquals(["test=bob", "price:10..20"], $split);
    }

    public function testSplitNoCategory()
    {
        $query = new Query();
        $split = $query->splitRefinements("~gender=Women~simpleColorDesc=Pink~product=Clothing");
        $this->assertEquals(["gender=Women", "simpleColorDesc=Pink", "product=Clothing"], $split);
    }

    public function testSplitCategory()
    {
        $query = new Query();
        $split = $query->splitRefinements("~category_leaf_expanded=Category Root~Athletics~Men's~Sneakers");
        $this->assertEquals(["category_leaf_expanded=Category Root~Athletics~Men's~Sneakers"], $split);
    }

    public function testSplitMultipleCategory()
    {
        $query = new Query();
        $split = $query->splitRefinements("~category_leaf_expanded=Category Root~Athletics~Men's~Sneakers~category_leaf_id=580003");
        $this->assertEquals(["category_leaf_expanded=Category Root~Athletics~Men's~Sneakers", "category_leaf_id=580003"], $split);
    }

    public function testSplitRangeAndMultipleCategory()
    {
        $query = new Query();
        $split = $query->splitRefinements("test=bob~price:10..20~category_leaf_expanded=Category Root~Athletics~Men's" .
        "~Sneakers~category_leaf_id=580003~color=BLUE~color=YELLOW~color=GREY");
        $this->assertEquals(["test=bob", "price:10..20",
                                       "category_leaf_expanded=Category Root~Athletics~Men's~Sneakers", "category_leaf_id=580003",
                                       "color=BLUE", "color=YELLOW", "color=GREY"], $split);
    }

    public function testSplitCategoryLong()
    {
        $query = new Query();
        $split = $query->splitRefinements("~category_leaf_expanded=Category Root~Athletics~Men's~Sneakers~category_leaf_id=580003~" .
            "color=BLUE~color=YELLOW~color=GREY~feature=Lace Up~feature=Light Weight~brand=Nike");
        $this->assertEquals(["category_leaf_expanded=Category Root~Athletics~Men's~Sneakers", "category_leaf_id=580003",
                        "color=BLUE", "color=YELLOW", "color=GREY", "feature=Lace Up", "feature=Light Weight",
                        "brand=Nike"], $split);
    }

    public function testNull()
    {
        $query = new Query();
        $split = $query->splitRefinements(null);
        $this->assertEquals([], $split);
    }

    public function testEmpty()
    {
        $query = new Query();
        $split = $query->splitRefinements("");
        $this->assertEquals([], $split);
    }

    public function testUtf8()
    {
        $query = new Query();
        $split = $query->splitRefinements("tëst=bäb~price:10..20");
        $this->assertEquals(["tëst=bäb", "price:10..20"], $split);
    }
}

QueryTest::init();

<?php

require_once __DIR__ . '/JsonSerializeTest.php';

use GroupByInc\API\Query;
use GroupByInc\API\Request\Sort;
use Httpful\Response;

class QueryTest extends PHPUnit_Framework_TestCase
{
    const QUERY = '{"clientKey":"XXXX-XXXX-XXXX-XXXX","collection":"testproducts","area":"Production","biasingProfile":"testProfile","language":"en","query":"the","sort":{"field":"price","order":"Descending"},"fields":["brand","category","height"],"orFields":["price","color"],"refinements":[{"navigationName":"green","exclude":true,"high":"delicious","low":"atrocious","type":"Range"},{"navigationName":"green","exclude":false,"value":"malaise","type":"Value"}],"customUrlParams":[{"key":"guava","value":"mango"}],"skip":20,"pageSize":14,"disableAutocorrection":true,"pruneRefinements":false,"wildcardSearchEnabled":true}';

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
        $this->assertEquals(self::QUERY, $json);
    }
}

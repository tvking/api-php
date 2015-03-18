<?php

require_once __DIR__ . '/JsonSerializeTest.php';

use GroupByInc\API\Model\BannerZone;
use GroupByInc\API\Model\Cluster;
use GroupByInc\API\Model\ClusterRecord;
use GroupByInc\API\Model\ContentZone;
use GroupByInc\API\Model\Metadata;
use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\PageInfo;
use GroupByInc\API\Model\Record;
use GroupByInc\API\Model\RecordsZone;
use GroupByInc\API\Model\RefinementRange;
use GroupByInc\API\Model\RefinementValue;
use GroupByInc\API\Model\Results;
use GroupByInc\API\Model\RichContentZone;
use GroupByInc\API\Model\SelectedRefinement;
use GroupByInc\API\Model\Template;
use GroupByInc\API\Util\SerializerFactory;
use JMS\Serializer\Serializer;

class JsonDeserializeTest extends PHPUnit_Framework_TestCase
{
    public static $JSON_RICH_CONTENT_ZONE = '{"content":"crestfallen","_id":"f90j1e1rf","name":"appalled","type":"Rich_Content"}';
    public static $JSON_CONTENT_ZONE = '{"content":"mushy","_id":"23425n89hr","name":"porcelain","type":"Content"}';
    public static $JSON_BANNER_ZONE = '{"content":"man","_id":"asf0j2380jf","name":"vitruvian","type":"Banner"}';
    public static $JSON_CLUSTER_RECORD = '{"title":"fubar","url":"example.com","snippet":"itty bit"}';
    public static $JSON_RECORD = '{"_id":"fw90314jh289t","_u":"exemplar.com","_snippet":"Curator","_t":"Periwinkle","allMeta":{"look":"at","all":"my","keys":["we","are","the","values"]}}';
    public static $JSON_METADATA = '{"key":"orange","value":"apple"}';
    public static $JSON_REFINEMENT_VALUE = '{"_id":"fadfs89y10j","count":987,"type":"Value","value":"malaise","exclude":false}';
    public static $JSON_REFINEMENT_RANGE = '{"high":"delicious","low":"atrocious","_id":"342h9582hh4","count":14,"type":"Range","exclude":true}';
    public static $JSON_PAGE_INFO = '{"recordStart":20,"recordEnd":50}';
    public static $JSON_RECORDS_ZONE;
    public static $JSON_TEMPLATE;
    public static $JSON_CLUSTER;
    public static $JSON_NAVIGATION;
    /** @var Serializer */
    private static $serializer;

    public static function setUpBeforeClass()
    {
        self::$serializer = SerializerFactory::build();
    }

    public static function init()
    {
        self::$JSON_RECORDS_ZONE = '{"records":[' . self::$JSON_RECORD .
            '],"_id":"1240jfw9s8","name":"gorbachev","type":"Record"}';

        self::$JSON_TEMPLATE = '{"_id":"fad87g114","name":"bulbous","ruleName":"carmageddon",' .
            '"zones":[' . self::$JSON_CONTENT_ZONE . ',' . self::$JSON_RECORDS_ZONE . ']}';

        self::$JSON_CLUSTER = '{"term":"some","records":[' . self::$JSON_CLUSTER_RECORD . ']}';

        self::$JSON_NAVIGATION = '{"_id":"081h29n81f","name":"green","displayName":"GReeN",' .
            '"range":true,"or":false,"type":"Range_Date","sort":"Value_Ascending","refinements":[' .
            self::$JSON_REFINEMENT_RANGE . ',' . self::$JSON_REFINEMENT_VALUE .
            '],"metadata":[' . self::$JSON_METADATA . ']}';
    }

    public function testDeserializeRefinementRange()
    {
        /** @var RefinementRange $refRange */
        $refRange = $this->deserialize(self::$JSON_REFINEMENT_RANGE, 'GroupByInc\API\Model\RefinementRange');
        $this->assertEquals(JsonSerializeTest::$OBJ_REFINEMENT_RANGE, $refRange);
    }

    private function deserialize($json, $namespacedClass)
    {
        return self::$serializer->deserialize($json, $namespacedClass, 'json');
    }

    public function testDeserializeRefinementValue()
    {
        /** @var RefinementValue $refValue */
        $refValue = $this->deserialize(self::$JSON_REFINEMENT_VALUE, 'GroupByInc\API\Model\RefinementValue');
        $this->assertEquals(JsonSerializeTest::$OBJ_REFINEMENT_VALUE, $refValue);
    }

    public function testDeserializeMetadata()
    {
        /** @var Metadata $metadata */
        $metadata = $this->deserialize(self::$JSON_METADATA, 'GroupByInc\API\Model\Metadata');
        $this->assertEquals(JsonSerializeTest::$OBJ_METADATA, $metadata);
    }

    public function testDeserializeNavigation()
    {
        /** @var Navigation $navigation */
        $navigation = $this->deserialize(self::$JSON_NAVIGATION, 'GroupByInc\API\Model\Navigation');
        $this->assertEquals(JsonSerializeTest::$OBJ_NAVIGATION, $navigation);
    }

    public function testDeserializeClusterRecord()
    {
        /** @var ClusterRecord $clusterRecord */
        $clusterRecord = $this->deserialize(self::$JSON_CLUSTER_RECORD, 'GroupByInc\API\Model\ClusterRecord');
        $this->assertEquals(JsonSerializeTest::$OBJ_CLUSTER_RECORD, $clusterRecord);
    }

    public function testDeserializeCluster()
    {
        /** @var Cluster $cluster */
        $cluster = $this->deserialize(self::$JSON_CLUSTER, 'GroupByInc\API\Model\Cluster');
        $this->assertEquals(JsonSerializeTest::$OBJ_CLUSTER, $cluster);
    }

    public function testDeserializeRecord()
    {
        /** @var Record $record */
        $record = $this->deserialize(self::$JSON_RECORD, 'GroupByInc\API\Model\Record');
        $this->assertEquals(JsonSerializeTest::$OBJ_RECORD, $record);
    }

    public function testDeserializePageInfo()
    {
        /** @var PageInfo $pageInfo */
        $pageInfo = $this->deserialize(self::$JSON_PAGE_INFO, 'GroupByInc\API\Model\PageInfo');

        $this->assertEquals(20, $pageInfo->getRecordStart());
        $this->assertEquals(50, $pageInfo->getRecordEnd());
    }

    public function testDeserializeContentZone()
    {
        /** @var ContentZone $contentZone */
        $contentZone = $this->deserialize(self::$JSON_CONTENT_ZONE, 'GroupByInc\API\Model\ContentZone');
        $this->assertEquals(JsonSerializeTest::$OBJ_CONTENT_ZONE, $contentZone);
    }

    public function testDeserializeRecordsZone()
    {
        /** @var RecordsZone $recordsZone */
        $recordsZone = $this->deserialize(self::$JSON_RECORDS_ZONE, 'GroupByInc\API\Model\RecordsZone');
        $this->assertEquals(JsonSerializeTest::$OBJ_RECORDS_ZONE, $recordsZone);
    }

    public function testDeserializeBannerZone()
    {
        /** @var BannerZone $bannerZone */
        $bannerZone = $this->deserialize(self::$JSON_BANNER_ZONE, 'GroupByInc\API\Model\BannerZone');
        $this->assertEquals(JsonSerializeTest::$OBJ_BANNER_ZONE, $bannerZone);
    }

    public function testDeserializeRichContentZone()
    {
        /** @var RichContentZone $richContentZone */
        $richContentZone = $this->deserialize(self::$JSON_RICH_CONTENT_ZONE, 'GroupByInc\API\Model\RichContentZone');
        $this->assertEquals(JsonSerializeTest::$OBJ_RICH_CONTENT_ZONE, $richContentZone);
    }

    public function testDeserializeTemplate()
    {
        /** @var Template $template */
        $template = $this->deserialize(self::$JSON_TEMPLATE, 'GroupByInc\API\Model\Template');
        $this->assertEquals(JsonSerializeTest::$OBJ_TEMPLATE, $template);
    }

    public function testDeserializeResults()
    {
        $expectedResults = new Results();
        $expectedResults->setArea("christmas");
        $expectedResults->setClusters(array(JsonSerializeTest::$OBJ_CLUSTER));
        $expectedResults->setAvailableNavigation(array(JsonSerializeTest::$OBJ_NAVIGATION));
        $expectedResults->setDidYouMean(array("square", "skewer"));
        $expectedResults->setErrors("criminey!");
        $expectedResults->setPageInfo(JsonSerializeTest::$OBJ_PAGE_INFO);
        $expectedResults->setQuery("skwuare");
        $expectedResults->setRecords(array(JsonSerializeTest::$OBJ_RECORD));
        $expectedResults->setRedirect("/to/the/moon.html");
        $expectedResults->setSelectedNavigation(array(JsonSerializeTest::$OBJ_NAVIGATION));
        $expectedResults->setTemplate(JsonSerializeTest::$OBJ_TEMPLATE);
        $expectedResults->setSiteParams(array(JsonSerializeTest::$OBJ_METADATA));
        $expectedResults->setRelatedQueries(array("squawk", "ask"));
        $expectedResults->setRewrites(array("Synonym", "Antonym", "Homonym"));
        $expectedResults->setTotalRecordCount(34);

        $json = '{"availableNavigation":[' . self::$JSON_NAVIGATION . '],' .
            '"selectedNavigation":[' . self::$JSON_NAVIGATION . '],' .
            '"clusters":[' . self::$JSON_CLUSTER . '],"records":[' . self::$JSON_RECORD . '],' .
            '"didYouMean":["square","skewer"],"relatedQueries":["squawk","ask"],' .
            '"siteParams":[' . self::$JSON_METADATA . '],"rewrites":["Synonym","Antonym","Homonym"],' .
            '"pageInfo":' . self::$JSON_PAGE_INFO . ',"template":' . self::$JSON_TEMPLATE . ',' .
            '"redirect":"/to/the/moon.html","errors":"criminey!","query":"skwuare","area":"christmas",' .
            '"totalRecordCount":34}';

        /** @var Results $results */
        $results = $this->deserialize($json, 'GroupByInc\API\Model\Results');
        $this->assertEquals($expectedResults, $results);
    }
}

JsonDeserializeTest::init();
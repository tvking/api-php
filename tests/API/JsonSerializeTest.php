<?php

require_once __DIR__ . '/JsonDeserializeTest.php';

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
use GroupByInc\API\Model\RestrictNavigation;
use GroupByInc\API\Model\RichContentZone;
use GroupByInc\API\Model\Template;
use GroupByInc\API\Model\Zone;
use GroupByInc\API\Request\CustomUrlParam;
use GroupByInc\API\Request\RefinementsRequest;
use GroupByInc\API\Request\Request;
use GroupByInc\API\Request\Sort;
use GroupByInc\API\Util\SerializerFactory;
use JMS\Serializer\Serializer;

class JsonSerializeTest extends PHPUnit_Framework_TestCase
{
    /** @var Metadata */
    public static $OBJ_METADATA;
    /** @var PageInfo */
    public static $OBJ_PAGE_INFO;
    /** @var Cluster */
    public static $OBJ_CLUSTER;
    /** @var ClusterRecord */
    public static $OBJ_CLUSTER_RECORD;
    /** @var RefinementRange */
    public static $OBJ_REFINEMENT_RANGE;
    /** @var RefinementValue */
    public static $OBJ_REFINEMENT_VALUE;
    /** @var Navigation */
    public static $OBJ_NAVIGATION;
    /** @var ContentZone */
    public static $OBJ_CONTENT_ZONE;
    /** @var BannerZone */
    public static $OBJ_BANNER_ZONE;
    /** @var RichContentZone */
    public static $OBJ_RICH_CONTENT_ZONE;
    /** @var RecordsZone */
    public static $OBJ_RECORDS_ZONE;
    /** @var Record */
    public static $OBJ_RECORD;
    /** @var Template */
    public static $OBJ_TEMPLATE;
    /** @var CustomUrlParam */
    public static $OBJ_CUSTOM_URL_PARAM;
    /** @var Request */
    public static $OBJ_REQUEST;
    /** @var RefinementsRequest */
    public static $OBJ_REFINEMENTS_REQUEST;
    /** @var Sort */
    public static $OBJ_SORT;
    /** @var RestrictNavigation */
    public static $OBJ_RESTRICT_NAVIGATION;
    /** @var Serializer */
    private static $serializer;

    public static function setUpBeforeClass()
    {
        self::$serializer = SerializerFactory::build();
    }

    public static function init()
    {
        self::$OBJ_METADATA = new Metadata();
        self::$OBJ_METADATA->setKey("orange")->setValue("apple");

        self::$OBJ_PAGE_INFO = new PageInfo();
        self::$OBJ_PAGE_INFO->setRecordStart(20)
            ->setRecordEnd(50);

        self::$OBJ_CLUSTER_RECORD = new ClusterRecord();
        self::$OBJ_CLUSTER_RECORD->setTitle("fubar")
            ->setUrl("example.com")
            ->setSnippet("itty bit");

        self::$OBJ_CLUSTER = new Cluster();
        self::$OBJ_CLUSTER->setRecords(array(self::$OBJ_CLUSTER_RECORD))
            ->setTerm("some");

        self::$OBJ_REFINEMENT_RANGE = new RefinementRange();
        self::$OBJ_REFINEMENT_RANGE->setId("342h9582hh4")
            ->setCount(14)
            ->setHigh("delicious")
            ->setLow("atrocious")
            ->setExclude(true);

        self::$OBJ_REFINEMENT_VALUE = new RefinementValue();
        self::$OBJ_REFINEMENT_VALUE->setId("fadfs89y10j")
            ->setCount(987)
            ->setValue("malaise")
            ->setExclude(false);

        self::$OBJ_NAVIGATION = new Navigation();
        self::$OBJ_NAVIGATION->setName("green")
            ->setDisplayName("GReeN")
            ->setId("081h29n81f")
            ->setOr(false)
            ->setType(Navigation\Type::Range_Date)
            ->setRange(true)
            ->setSort(Navigation\Order::Value_Ascending)
            ->setMetadata(array(self::$OBJ_METADATA))
            ->setRefinements(array(self::$OBJ_REFINEMENT_RANGE, self::$OBJ_REFINEMENT_VALUE))
            ->setMoreRefinements(true);

        self::$OBJ_RECORD = new Record();
        self::$OBJ_RECORD->setId("fw90314jh289t")
            ->setTitle("Periwinkle")
            ->setSnippet("Curator")
            ->setUrl("exemplar.com")
            ->setAllMeta(array(
                "look" => "at",
                "all" => "my",
                "keys" => array("we", "are", "the", "values")
            ));

        self::$OBJ_CONTENT_ZONE = new ContentZone();
        self::$OBJ_CONTENT_ZONE->setId("23425n89hr")
            ->setName("porcelain")
            ->setContent("mushy");

        self::$OBJ_BANNER_ZONE = new BannerZone();
        self::$OBJ_BANNER_ZONE->setId("asf0j2380jf")
            ->setName("vitruvian")
            ->setBannerUrl("man");

        self::$OBJ_RICH_CONTENT_ZONE = new RichContentZone();
        self::$OBJ_RICH_CONTENT_ZONE->setId("f90j1e1rf")
            ->setName("appalled")
            ->setRichContent("crestfallen");

        self::$OBJ_RECORDS_ZONE = new RecordsZone();
        self::$OBJ_RECORDS_ZONE->setId("1240jfw9s8")
            ->setName("gorbachev")
            ->setRecords(array(self::$OBJ_RECORD));

        self::$OBJ_TEMPLATE = new Template();
        self::$OBJ_TEMPLATE->setId("fad87g114")
            ->setName("bulbous")
            ->setRuleName("carmageddon")
            ->setZones(array(self::$OBJ_CONTENT_ZONE, self::$OBJ_RECORDS_ZONE));

        self::$OBJ_CUSTOM_URL_PARAM = new CustomUrlParam();
        self::$OBJ_CUSTOM_URL_PARAM->setKey("guava")->setValue("mango");

        self::$OBJ_SORT = new Sort();
        self::$OBJ_SORT->setOrder(Sort\Order::Descending)
            ->setField("price");

        self::$OBJ_RESTRICT_NAVIGATION = new RestrictNavigation();
        self::$OBJ_RESTRICT_NAVIGATION->setCount(2)
            ->setName("categories");

        self::$OBJ_REQUEST = new Request();
        self::$OBJ_REQUEST->clientKey = "adf7h8er7h2r";
        self::$OBJ_REQUEST->collection = "ducks";
        self::$OBJ_REQUEST->area = "surface";
        self::$OBJ_REQUEST->skip = 12;
        self::$OBJ_REQUEST->pageSize = 30;
        self::$OBJ_REQUEST->biasingProfile = "ballooning";
        self::$OBJ_REQUEST->language = "en";
        self::$OBJ_REQUEST->pruneRefinements = true;
        self::$OBJ_REQUEST->returnBinary = false;
        self::$OBJ_REQUEST->query = "cantaloupe";
        self::$OBJ_REQUEST->refinementQuery = "cranberry";
        self::$OBJ_REQUEST->sort = self::$OBJ_SORT;
        self::$OBJ_REQUEST->fields = array("pineapple", "grape", "clementine");
        self::$OBJ_REQUEST->orFields = array("pumpernickel", "rye");
        self::$OBJ_REQUEST->refinements = array(self::$OBJ_REFINEMENT_RANGE, self::$OBJ_REFINEMENT_VALUE);
        self::$OBJ_REQUEST->customUrlParams = array(self::$OBJ_CUSTOM_URL_PARAM);
        self::$OBJ_REQUEST->wildcardSearchEnabled = true;
        self::$OBJ_REQUEST->restrictNavigation = self::$OBJ_RESTRICT_NAVIGATION;

        self::$OBJ_REFINEMENTS_REQUEST = new RefinementsRequest();
        self::$OBJ_REFINEMENTS_REQUEST->clientKey = "adf7h8er7h2r";
        self::$OBJ_REFINEMENTS_REQUEST->collection = "ducks";
        self::$OBJ_REFINEMENTS_REQUEST->area = "surface";
        self::$OBJ_REFINEMENTS_REQUEST->skip = 12;
        self::$OBJ_REFINEMENTS_REQUEST->pageSize = 30;
        self::$OBJ_REFINEMENTS_REQUEST->biasingProfile = "ballooning";
        self::$OBJ_REFINEMENTS_REQUEST->language = "en";
        self::$OBJ_REFINEMENTS_REQUEST->pruneRefinements = true;
        self::$OBJ_REFINEMENTS_REQUEST->returnBinary = false;
        self::$OBJ_REFINEMENTS_REQUEST->query = "cantaloupe";
        self::$OBJ_REFINEMENTS_REQUEST->refinementQuery = "cranberry";
        self::$OBJ_REFINEMENTS_REQUEST->sort = self::$OBJ_SORT;
        self::$OBJ_REFINEMENTS_REQUEST->fields = array("pineapple", "grape", "clementine");
        self::$OBJ_REFINEMENTS_REQUEST->orFields = array("pumpernickel", "rye");
        self::$OBJ_REFINEMENTS_REQUEST->refinements = array(self::$OBJ_REFINEMENT_RANGE, self::$OBJ_REFINEMENT_VALUE);
        self::$OBJ_REFINEMENTS_REQUEST->customUrlParams = array(self::$OBJ_CUSTOM_URL_PARAM);
        self::$OBJ_REFINEMENTS_REQUEST->wildcardSearchEnabled = true;
        self::$OBJ_REFINEMENTS_REQUEST->restrictNavigation = self::$OBJ_RESTRICT_NAVIGATION;
        self::$OBJ_REFINEMENTS_REQUEST->originalQuery = self::$OBJ_REQUEST;
        self::$OBJ_REFINEMENTS_REQUEST->navigationName = "height";
    }

    public function setUp()
    {

    }

    public function testEncodePageInfo()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_PAGE_INFO,
            $this->serialize(self::$OBJ_PAGE_INFO));
    }

    /**
     * @param object $obj
     *
     * @return string
     */
    private function serialize($obj)
    {
        return self::$serializer->serialize($obj, 'json');
    }

    public function testEncodeCluster()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_CLUSTER,
            $this->serialize(self::$OBJ_CLUSTER));
    }

    public function testEncodeClusterRecord()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_CLUSTER_RECORD,
            $this->serialize(self::$OBJ_CLUSTER_RECORD));
    }

    public function testEncodeCustomUrlParam()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_CUSTOM_URL_PARAM,
            $this->serialize(self::$OBJ_CUSTOM_URL_PARAM));
    }

    public function testEncodeMetadata()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_METADATA,
            $this->serialize(self::$OBJ_METADATA));
    }

    public function testEncodeNavigation()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_NAVIGATION,
            $this->serialize(self::$OBJ_NAVIGATION));
    }

    public function testEncodeRecord()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_RECORD,
            $this->serialize(self::$OBJ_RECORD));
    }

    public function testEncodeRefinementRange()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_REFINEMENT_RANGE,
            $this->serialize(self::$OBJ_REFINEMENT_RANGE));
    }

    public function testEncodeRefinementValue()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_REFINEMENT_VALUE,
            $this->serialize(self::$OBJ_REFINEMENT_VALUE));
    }

    public function testEncodeContentZone()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_CONTENT_ZONE,
            $this->serialize(self::$OBJ_CONTENT_ZONE));
    }

    public function testEncodeBannerZone()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_BANNER_ZONE,
            $this->serialize(self::$OBJ_BANNER_ZONE));
    }

    public function testEncodeRichContentZone()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_RICH_CONTENT_ZONE,
            $this->serialize(self::$OBJ_RICH_CONTENT_ZONE));
    }

    public function testEncodeRecordsZone()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_RECORDS_ZONE,
            $this->serialize(self::$OBJ_RECORDS_ZONE));
    }

    public function testEncodeSort()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_SORT, $this->serialize(self::$OBJ_SORT));
    }

    public function testEncodeTemplate()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_TEMPLATE,
            $this->serialize(self::$OBJ_TEMPLATE));
    }

    public function testEncodeRestrictNavigation()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_RESTRICT_NAVIGATION,
            $this->serialize(self::$OBJ_RESTRICT_NAVIGATION));
    }

    public function testEncodeRequest()
    {
        $this->assertJsonStringEqualsJsonString(JsonDeserializeTest::$JSON_REQUEST, $this->serialize(self::$OBJ_REQUEST));
    }

    public function testEncodeRefinementsRequest()
    {
        $this->assertJsonStringEqualsJsonString('{"originalQuery":' . JsonDeserializeTest::$JSON_REQUEST .
            ',"navigationName":"height"}', $this->serialize(self::$OBJ_REFINEMENTS_REQUEST));
    }
}

JsonSerializeTest::init();

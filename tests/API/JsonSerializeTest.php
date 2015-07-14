<?php

require_once __DIR__ . '/Expectation/Json.php';
require_once __DIR__ . '/Expectation/Object.php';

use GroupByInc\API\Util\SerializerFactory;
use JMS\Serializer\Serializer;

class JsonSerializeTest extends PHPUnit_Framework_TestCase
{

    /** @var Serializer */
    private static $serializer;

    public static function setUpBeforeClass()
    {
        self::$serializer = SerializerFactory::build();
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

    public function testEncodePageInfo()
    {
        $this->assertJsonStringEqualsJsonString(Json::$PAGE_INFO,
            $this->serialize(Object::$PAGE_INFO));
    }

    public function testEncodeCluster()
    {
        $this->assertJsonStringEqualsJsonString(Json::$CLUSTER,
            $this->serialize(Object::$CLUSTER));
    }

    public function testEncodeClusterRecord()
    {
        $this->assertJsonStringEqualsJsonString(Json::$CLUSTER_RECORD,
            $this->serialize(Object::$CLUSTER_RECORD));
    }

    public function testEncodeCustomUrlParam()
    {
        $this->assertJsonStringEqualsJsonString(Json::$CUSTOM_URL_PARAM,
            $this->serialize(Object::$CUSTOM_URL_PARAM));
    }

    public function testEncodeMetadata()
    {
        $this->assertJsonStringEqualsJsonString(Json::$METADATA,
            $this->serialize(Object::$METADATA));
    }

    public function testEncodeNavigation()
    {
        $this->assertJsonStringEqualsJsonString(Json::$NAVIGATION,
            $this->serialize(Object::$NAVIGATION));
    }

    public function testEncodeRefinementMatchValue()
    {
        $this->assertJsonStringEqualsJsonString(Json::$REFINEMENT_MATCH_VALUE,
            $this->serialize(Object::$REFINEMENT_MATCH_VALUE));
    }

    public function testEncodeRefinementMatch()
    {
        $this->assertJsonStringEqualsJsonString(Json::$REFINEMENT_MATCH,
            $this->serialize(Object::$REFINEMENT_MATCH));
    }

    public function testEncodeRecord()
    {
        $this->assertJsonStringEqualsJsonString(Json::$RECORD,
            $this->serialize(Object::$RECORD));
    }

    public function testEncodeRefinementRange()
    {
        $this->assertJsonStringEqualsJsonString(Json::$REFINEMENT_RANGE,
            $this->serialize(Object::$REFINEMENT_RANGE));
    }

    public function testEncodeRefinementValue()
    {
        $this->assertJsonStringEqualsJsonString(Json::$REFINEMENT_VALUE,
            $this->serialize(Object::$REFINEMENT_VALUE));
    }

    public function testEncodeContentZone()
    {
        $this->assertJsonStringEqualsJsonString(Json::$CONTENT_ZONE,
            $this->serialize(Object::$CONTENT_ZONE));
    }

    public function testEncodeBannerZone()
    {
        $this->assertJsonStringEqualsJsonString(Json::$BANNER_ZONE,
            $this->serialize(Object::$BANNER_ZONE));
    }

    public function testEncodeRichContentZone()
    {
        $this->assertJsonStringEqualsJsonString(Json::$RICH_CONTENT_ZONE,
            $this->serialize(Object::$RICH_CONTENT_ZONE));
    }

    public function testEncodeRecordZone()
    {
        $this->assertJsonStringEqualsJsonString(Json::$RECORD_ZONE,
            $this->serialize(Object::$RECORD_ZONE));
    }

    public function testEncodeSort()
    {
        $this->assertJsonStringEqualsJsonString(Json::$SORT, $this->serialize(Object::$SORT));
    }

    public function testEncodeTemplate()
    {
        $this->assertJsonStringEqualsJsonString(Json::$TEMPLATE,
            $this->serialize(Object::$TEMPLATE));
    }

    public function testEncodeRestrictNavigation()
    {
        $this->assertJsonStringEqualsJsonString(Json::$RESTRICT_NAVIGATION,
            $this->serialize(Object::$RESTRICT_NAVIGATION));
    }

    public function testEncodePartialMatchRule()
    {
        $this->assertJsonStringEqualsJsonString(Json::$PARTIAL_MATCH_RULE,
            $this->serialize(Object::$PARTIAL_MATCH_RULE));
    }

    public function testEncodeMatchStrategy()
    {
        $this->assertJsonStringEqualsJsonString(Json::$MATCH_STRATEGY,
            $this->serialize(Object::$MATCH_STRATEGY));
    }

    public function testEncodeRequest()
    {
        $this->assertJsonStringEqualsJsonString(Json::$REQUEST, $this->serialize(Object::$REQUEST));
    }

    public function testEncodeRefinementsRequest()
    {
        $this->assertJsonStringEqualsJsonString(JSON::$REFINEMENTS_REQUEST,
            $this->serialize(Object::$REFINEMENTS_REQUEST));
    }
}

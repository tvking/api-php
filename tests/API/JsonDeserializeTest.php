<?php

require_once __DIR__ . '/Expectation/Json.php';
require_once __DIR__ . '/Expectation/Object.php';

use GroupByInc\API\Model\BannerZone;
use GroupByInc\API\Model\Cluster;
use GroupByInc\API\Model\ClusterRecord;
use GroupByInc\API\Model\ContentZone;
use GroupByInc\API\Model\Metadata;
use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\PageInfo;
use GroupByInc\API\Model\Record;
use GroupByInc\API\Model\RecordZone;
use GroupByInc\API\Model\RefinementMatch;
use GroupByInc\API\Model\RefinementRange;
use GroupByInc\API\Model\RefinementsResult;
use GroupByInc\API\Model\RefinementValue;
use GroupByInc\API\Model\Results;
use GroupByInc\API\Model\RichContentZone;
use GroupByInc\API\Model\Template;
use GroupByInc\API\Request\RestrictNavigation;
use GroupByInc\API\Util\SerializerFactory;
use JMS\Serializer\Serializer;

class JsonDeserializeTest extends PHPUnit_Framework_TestCase
{

    /** @var Serializer */
    private static $serializer;

    public static function setUpBeforeClass()
    {
        self::$serializer = SerializerFactory::build();
    }

    private function deserialize($json, $namespacedClass)
    {
        return self::$serializer->deserialize($json, $namespacedClass, 'json');
    }

    public function testDeserializeRefinementRange()
    {
        /** @var RefinementRange $refRange */
        $refRange = $this->deserialize(Json::$REFINEMENT_RANGE, 'GroupByInc\API\Model\RefinementRange');
        $this->assertEquals(Object::$REFINEMENT_RANGE, $refRange);
    }

    public function testDeserializeRefinementValue()
    {
        /** @var RefinementValue $refValue */
        $refValue = $this->deserialize(Json::$REFINEMENT_VALUE, 'GroupByInc\API\Model\RefinementValue');
        $this->assertEquals(Object::$REFINEMENT_VALUE, $refValue);
    }

    public function testDeserializeMetadata()
    {
        /** @var Metadata $metadata */
        $metadata = $this->deserialize(Json::$METADATA, 'GroupByInc\API\Model\Metadata');
        $this->assertEquals(Object::$METADATA, $metadata);
    }

    public function testDeserializeNavigation()
    {
        /** @var Navigation $navigation */
        $navigation = $this->deserialize(Json::$NAVIGATION, 'GroupByInc\API\Model\Navigation');
        $this->assertEquals(Object::$NAVIGATION, $navigation);
    }

    public function testDeserializeClusterRecord()
    {
        /** @var ClusterRecord $clusterRecord */
        $clusterRecord = $this->deserialize(Json::$CLUSTER_RECORD, 'GroupByInc\API\Model\ClusterRecord');
        $this->assertEquals(Object::$CLUSTER_RECORD, $clusterRecord);
    }

    public function testDeserializeCluster()
    {
        /** @var Cluster $cluster */
        $cluster = $this->deserialize(Json::$CLUSTER, 'GroupByInc\API\Model\Cluster');
        $this->assertEquals(Object::$CLUSTER, $cluster);
    }

    public function testDeserializeRefinementMatchValue()
    {
        /** @var RefinementMatch\Value $refinementMatchValue */
        $refinementMatchValue = $this->deserialize(Json::$REFINEMENT_MATCH_VALUE,
            'GroupByInc\API\Model\RefinementMatch\Value');
        $this->assertEquals(Object::$REFINEMENT_MATCH_VALUE, $refinementMatchValue);
    }

    public function testDeserializeRefinementMatch()
    {
        /** @var RefinementMatch $refinementMatch */
        $refinementMatch = $this->deserialize(Json::$REFINEMENT_MATCH, 'GroupByInc\API\Model\RefinementMatch');
        $this->assertEquals(Object::$REFINEMENT_MATCH, $refinementMatch);
    }

    public function testDeserializeRecord()
    {
        /** @var Record $record */
        $record = $this->deserialize(Json::$RECORD, 'GroupByInc\API\Model\Record');
        $this->assertEquals(Object::$RECORD, $record);
    }

    public function testDeserializePageInfo()
    {
        /** @var PageInfo $pageInfo */
        $pageInfo = $this->deserialize(Json::$PAGE_INFO, 'GroupByInc\API\Model\PageInfo');
        $this->assertEquals(Object::$PAGE_INFO, $pageInfo);
    }

    public function testDeserializeContentZone()
    {
        /** @var ContentZone $contentZone */
        $contentZone = $this->deserialize(Json::$CONTENT_ZONE, 'GroupByInc\API\Model\ContentZone');
        $this->assertEquals(Object::$CONTENT_ZONE, $contentZone);
    }

    public function testDeserializeRecordZone()
    {
        /** @var RecordZone $recordZone */
        $recordZone = $this->deserialize(Json::$RECORD_ZONE, 'GroupByInc\API\Model\RecordZone');
        $this->assertEquals(Object::$RECORD_ZONE, $recordZone);
    }

    public function testDeserializeBannerZone()
    {
        /** @var BannerZone $bannerZone */
        $bannerZone = $this->deserialize(Json::$BANNER_ZONE, 'GroupByInc\API\Model\BannerZone');
        $this->assertEquals(Object::$BANNER_ZONE, $bannerZone);
    }

    public function testDeserializeRichContentZone()
    {
        /** @var RichContentZone $richContentZone */
        $richContentZone = $this->deserialize(Json::$RICH_CONTENT_ZONE, 'GroupByInc\API\Model\RichContentZone');
        $this->assertEquals(Object::$RICH_CONTENT_ZONE, $richContentZone);
    }

    public function testDeserializeTemplate()
    {
        /** @var Template $template */
        $template = $this->deserialize(Json::$TEMPLATE, 'GroupByInc\API\Model\Template');
        $this->assertEquals(Object::$TEMPLATE, $template);
    }

    public function testDeserializeRestrictNavigation()
    {
        /** @var RestrictNavigation $restrictNavigation */
        $restrictNavigation = $this->deserialize(Json::$RESTRICT_NAVIGATION, 'GroupByInc\API\Request\RestrictNavigation');
        $this->assertEquals(Object::$RESTRICT_NAVIGATION, $restrictNavigation);
    }

    public function testDeserializeResults()
    {
        /** @var Results $results */
        $results = $this->deserialize(Json::$RESULTS, 'GroupByInc\API\Model\Results');
        $this->assertEquals(Object::$RESULTS, $results);
    }

    public function testDeserializeRefinementsResult()
    {
        /** @var RefinementsResult $refinementsResult */
        $refinementsResult = $this->deserialize(Json::$REFINEMENT_RESULTS, 'GroupByInc\API\Model\RefinementsResult');
        $this->assertEquals(Object::$REFINEMENTS_RESULT, $refinementsResult);
    }
}
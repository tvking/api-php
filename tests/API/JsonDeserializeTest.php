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

        $json = json_decode(Json::$REFINEMENT_RANGE);
        $this->assertEquals($json->high, $refRange->getHigh());
        $this->assertEquals($json->low, $refRange->getLow());
        $this->assertEquals($json->type, $refRange->getType());
        $this->assertEquals($json->count, $refRange->getCount());
        $this->assertEquals(true, $refRange->isRange());
        $this->assertEquals($json->exclude, $refRange->isExclude());
    }

    public function testDeserializeRefinementValue()
    {
        /** @var RefinementValue $refValue */
        $refValue = $this->deserialize(Json::$REFINEMENT_VALUE, 'GroupByInc\API\Model\RefinementValue');
        $this->assertEquals(Object::$REFINEMENT_VALUE, $refValue);

        $json = json_decode(Json::$REFINEMENT_VALUE);
        $this->assertEquals($json->value, $refValue->getValue());
        $this->assertEquals($json->type, $refValue->getType());
        $this->assertEquals($json->count, $refValue->getCount());
        $this->assertEquals(false, $refValue->isRange());
        $this->assertEquals($json->exclude, $refValue->isExclude());
    }

    public function testDeserializeMetadata()
    {
        /** @var Metadata $metadata */
        $metadata = $this->deserialize(Json::$METADATA, 'GroupByInc\API\Model\Metadata');
        $this->assertEquals(Object::$METADATA, $metadata);

        $json = json_decode(Json::$METADATA);
        $this->assertEquals($json->key, $metadata->getKey());
        $this->assertEquals($json->value, $metadata->getValue());
    }

    public function testDeserializeNavigation()
    {
        /** @var Navigation $navigation */
        $navigation = $this->deserialize(Json::$NAVIGATION, 'GroupByInc\API\Model\Navigation');
        $this->assertEquals(Object::$NAVIGATION, $navigation);

        $json = json_decode(Json::$NAVIGATION);
        $this->assertEquals($json->name, $navigation->getName());
        $this->assertEquals($json->displayName, $navigation->getDisplayName());
        $this->assertEquals($json->type, $navigation->getType());
        $this->assertEquals(Object::$SORT, $navigation->getSort());
        $this->assertEquals([Object::$METADATA], $navigation->getMetadata());
        $this->assertEquals([Object::$REFINEMENT_RANGE, Object::$REFINEMENT_VALUE], $navigation->getRefinements());
        $this->assertEquals($json->moreRefinements, $navigation->getMoreRefinements());
    }

    public function testDeserializeClusterRecord()
    {
        /** @var ClusterRecord $clusterRecord */
        $clusterRecord = $this->deserialize(Json::$CLUSTER_RECORD, 'GroupByInc\API\Model\ClusterRecord');
        $this->assertEquals(Object::$CLUSTER_RECORD, $clusterRecord);

        $json = json_decode(Json::$CLUSTER_RECORD);
        $this->assertEquals($json->snippet, $clusterRecord->getSnippet());
        $this->assertEquals($json->title, $clusterRecord->getTitle());
        $this->assertEquals($json->url, $clusterRecord->getUrl());
    }

    public function testDeserializeCluster()
    {
        /** @var Cluster $cluster */
        $cluster = $this->deserialize(Json::$CLUSTER, 'GroupByInc\API\Model\Cluster');
        $this->assertEquals(Object::$CLUSTER, $cluster);

        $json = json_decode(Json::$CLUSTER);
        $this->assertEquals($json->term, $cluster->getTerm());
        $this->assertEquals([Object::$CLUSTER_RECORD], $cluster->getRecords());
    }

    public function testDeserializeRefinementMatchValue()
    {
        /** @var RefinementMatch\Value $refinementMatchValue */
        $refinementMatchValue = $this->deserialize(Json::$REFINEMENT_MATCH_VALUE,
            'GroupByInc\API\Model\RefinementMatch\Value');
        $this->assertEquals(Object::$REFINEMENT_MATCH_VALUE, $refinementMatchValue);

        $json = json_decode(Json::$REFINEMENT_MATCH_VALUE);
        $this->assertEquals($json->count, $refinementMatchValue->getCount());
        $this->assertEquals($json->value, $refinementMatchValue->getValue());
    }

    public function testDeserializeRefinementMatch()
    {
        /** @var RefinementMatch $refinementMatch */
        $refinementMatch = $this->deserialize(Json::$REFINEMENT_MATCH, 'GroupByInc\API\Model\RefinementMatch');
        $this->assertEquals(Object::$REFINEMENT_MATCH, $refinementMatch);

        $json = json_decode(Json::$REFINEMENT_MATCH);
        $this->assertEquals($json->name, $refinementMatch->getName());
        $this->assertEquals([Object::$REFINEMENT_MATCH_VALUE], $refinementMatch->getValues());
    }

    public function testDeserializeRecord()
    {
        /** @var Record $record */
        $record = $this->deserialize(Json::$RECORD, 'GroupByInc\API\Model\Record');
        $this->assertEquals(Object::$RECORD, $record);

        $json = json_decode(Json::$RECORD);
        $this->assertNotEmpty($record->getAllMeta());
        $this->assertEquals($json->_snippet, $record->getSnippet());
        $this->assertEquals($json->_t, $record->getTitle());
        $this->assertEquals($json->_u, $record->getUrl());
        $this->assertEquals([Object::$REFINEMENT_MATCH], $record->getRefinementMatches());
    }

    public function testDeserializePageInfo()
    {
        /** @var PageInfo $pageInfo */
        $pageInfo = $this->deserialize(Json::$PAGE_INFO, 'GroupByInc\API\Model\PageInfo');
        $this->assertEquals(Object::$PAGE_INFO, $pageInfo);

        $json = json_decode(Json::$PAGE_INFO);
        $this->assertEquals($json->recordStart, $pageInfo->getRecordStart());
        $this->assertEquals($json->recordEnd, $pageInfo->getRecordEnd());
    }

    public function testDeserializeContentZone()
    {
        /** @var ContentZone $contentZone */
        $contentZone = $this->deserialize(Json::$CONTENT_ZONE, 'GroupByInc\API\Model\ContentZone');
        $this->assertEquals(Object::$CONTENT_ZONE, $contentZone);

        $json = json_decode(Json::$CONTENT_ZONE);
        $this->assertEquals($json->name, $contentZone->getName());
        $this->assertEquals($json->content, $contentZone->getContent());
        $this->assertEquals($json->type, $contentZone->getType());
    }

    public function testDeserializeRecordZone()
    {
        /** @var RecordZone $recordZone */
        $recordZone = $this->deserialize(Json::$RECORD_ZONE, 'GroupByInc\API\Model\RecordZone');
        $this->assertEquals(Object::$RECORD_ZONE, $recordZone);

        $json = json_decode(Json::$RECORD_ZONE);
        $this->assertEquals($json->name, $recordZone->getName());
        $this->assertEquals($json->query, $recordZone->getQuery());
        $this->assertEquals([Object::$RECORD], $recordZone->getRecords());
        $this->assertEquals($json->type, $recordZone->getType());
    }

    public function testDeserializeBannerZone()
    {
        /** @var BannerZone $bannerZone */
        $bannerZone = $this->deserialize(Json::$BANNER_ZONE, 'GroupByInc\API\Model\BannerZone');
        $this->assertEquals(Object::$BANNER_ZONE, $bannerZone);

        $json = json_decode(Json::$BANNER_ZONE);
        $this->assertEquals($json->name, $bannerZone->getName());
        $this->assertEquals($json->bannerUrl, $bannerZone->getBannerUrl());
        $this->assertEquals($json->type, $bannerZone->getType());
    }

    public function testDeserializeRichContentZone()
    {
        /** @var RichContentZone $richContentZone */
        $richContentZone = $this->deserialize(Json::$RICH_CONTENT_ZONE, 'GroupByInc\API\Model\RichContentZone');
        $this->assertEquals(Object::$RICH_CONTENT_ZONE, $richContentZone);

        $json = json_decode(Json::$RICH_CONTENT_ZONE);
        $this->assertEquals($json->name, $richContentZone->getName());
        $this->assertEquals($json->richContent, $richContentZone->getRichContent());
        $this->assertEquals($json->type, $richContentZone->getType());
    }

    public function testDeserializeTemplate()
    {
        /** @var Template $template */
        $template = $this->deserialize(Json::$TEMPLATE, 'GroupByInc\API\Model\Template');
        $this->assertEquals(Object::$TEMPLATE, $template);

        $json = json_decode(Json::$TEMPLATE);
        $this->assertEquals($json->name, $template->getName());
        $this->assertEquals($json->ruleName, $template->getRuleName());
        $this->assertEquals([Object::$CONTENT_ZONE, Object::$RECORD_ZONE], $template->getZones());
    }

    public function testDeserializeRestrictNavigation()
    {
        /** @var RestrictNavigation $restrictNavigation */
        $restrictNavigation = $this->deserialize(Json::$RESTRICT_NAVIGATION, 'GroupByInc\API\Request\RestrictNavigation');
        $this->assertEquals(Object::$RESTRICT_NAVIGATION, $restrictNavigation);

        $json = json_decode(Json::$RESTRICT_NAVIGATION);
        $this->assertEquals($json->name, $restrictNavigation->getName());
        $this->assertEquals($json->count, $restrictNavigation->getCount());
    }

    public function testDeserializeResults()
    {
        /** @var Results $results */
        $results = $this->deserialize(Json::$RESULTS, 'GroupByInc\API\Model\Results');
        $this->assertEquals(Object::$RESULTS, $results);

        $json = json_decode(Json::$RESULTS);
        $this->assertEquals($json->area, $results->getArea());
        $this->assertEquals($json->query, $results->getQuery());
        $this->assertEquals($json->correctedQuery, $results->getCorrectedQuery());
        $this->assertEquals($json->errors, $results->getErrors());
        $this->assertEquals($json->originalQuery, $results->getOriginalQuery());
        $this->assertEquals($json->redirect, $results->getRedirect());
        $this->assertEquals($json->biasingProfile, $results->getBiasingProfile());
        $this->assertEquals($json->totalRecordCount, $results->getTotalRecordCount());
        $this->assertEquals($json->didYouMean, $results->getDidYouMean());
        $this->assertEquals($json->relatedQueries, $results->getRelatedQueries());
        $this->assertEquals($json->rewrites, $results->getRewrites());
        $this->assertEquals(Object::$PAGE_INFO, $results->getPageInfo());
        $this->assertEquals(Object::$TEMPLATE, $results->getTemplate());
        $this->assertEquals([Object::$RECORD], $results->getRecords());
        $this->assertEquals([Object::$NAVIGATION], $results->getAvailableNavigation());
        $this->assertEquals([Object::$CLUSTER], $results->getClusters());
        $this->assertEquals([Object::$METADATA], $results->getSiteParams());
        $this->assertEquals([Object::$NAVIGATION], $results->getSelectedNavigation());
    }

    public function testDeserializeRefinementsResult()
    {
        /** @var RefinementsResult $refinementsResult */
        $refinementsResult = $this->deserialize(Json::$REFINEMENT_RESULTS, 'GroupByInc\API\Model\RefinementsResult');
        $this->assertEquals(Object::$REFINEMENTS_RESULT, $refinementsResult);

        $json = json_decode(Json::$REFINEMENT_RESULTS);
        $this->assertEquals($json->errors, $refinementsResult->getErrors());
        $this->assertEquals(Object::$NAVIGATION, $refinementsResult->getNavigation());
    }
}
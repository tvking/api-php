<?php

use GroupByInc\API\Model\BannerZone;
use GroupByInc\API\Model\Cluster;
use GroupByInc\API\Model\ClusterRecord;
use GroupByInc\API\Model\ContentZone;
use GroupByInc\API\Model\CustomUrlParam;
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
use GroupByInc\API\Model\Sort;
use GroupByInc\API\Model\Template;
use GroupByInc\API\Model\Zone;
use GroupByInc\API\Request\MatchStrategy;
use GroupByInc\API\Request\PartialMatchRule;
use GroupByInc\API\Request\RefinementsRequest;
use GroupByInc\API\Request\Request;
use GroupByInc\API\Request\RestrictNavigation;

class Object
{
    /** @var Metadata */
    public static $METADATA;
    /** @var PageInfo */
    public static $PAGE_INFO;
    /** @var Cluster */
    public static $CLUSTER;
    /** @var ClusterRecord */
    public static $CLUSTER_RECORD;
    /** @var RefinementRange */
    public static $REFINEMENT_RANGE;
    /** @var RefinementValue */
    public static $REFINEMENT_VALUE;
    /** @var Navigation */
    public static $NAVIGATION;
    /** @var ContentZone */
    public static $CONTENT_ZONE;
    /** @var BannerZone */
    public static $BANNER_ZONE;
    /** @var RichContentZone */
    public static $RICH_CONTENT_ZONE;
    /** @var RecordZone */
    public static $RECORD_ZONE;
    /** @var RefinementMatch\Value */
    public static $REFINEMENT_MATCH_VALUE;
    /** @var RefinementMatch */
    public static $REFINEMENT_MATCH;
    /** @var Record */
    public static $RECORD;
    /** @var Template */
    public static $TEMPLATE;
    /** @var CustomUrlParam */
    public static $CUSTOM_URL_PARAM;
    /** @var Request */
    public static $REQUEST;
    /** @var RefinementsRequest */
    public static $REFINEMENTS_REQUEST;
    /** @var Sort */
    public static $SORT;
    /** @var RestrictNavigation */
    public static $RESTRICT_NAVIGATION;
    /** @var PartialMatchRule */
    public static $PARTIAL_MATCH_RULE;
    /** @var MatchStrategy */
    public static $MATCH_STRATEGY;
    /** @var Results */
    public static $RESULTS;
    /** @var RefinementsResult */
    public static $REFINEMENTS_RESULT;

    public static function init()
    {
        self::$METADATA = new Metadata();
        self::$METADATA->setKey("orange")->setValue("apple");

        self::$PAGE_INFO = new PageInfo();
        self::$PAGE_INFO->setRecordStart(20)
            ->setRecordEnd(50);

        self::$CLUSTER_RECORD = new ClusterRecord();
        self::$CLUSTER_RECORD->setTitle("fubar")
            ->setUrl("example.com")
            ->setSnippet("itty bit");

        self::$CLUSTER = new Cluster();
        self::$CLUSTER->setRecords(array(self::$CLUSTER_RECORD))
            ->setTerm("some");

        self::$REFINEMENT_RANGE = new RefinementRange();
        self::$REFINEMENT_RANGE->setId("342h9582hh4")
            ->setCount(14)
            ->setHigh("delicious")
            ->setLow("atrocious")
            ->setExclude(true);

        self::$REFINEMENT_VALUE = new RefinementValue();
        self::$REFINEMENT_VALUE->setId("fadfs89y10j")
            ->setCount(987)
            ->setValue("malaise")
            ->setExclude(false);

        self::$SORT = new Sort();
        self::$SORT->setOrder(Sort\Order::Descending)
            ->setField("price");

        self::$NAVIGATION = new Navigation();
        self::$NAVIGATION->setName("green")
            ->setDisplayName("GReeN")
            ->setId("081h29n81f")
            ->setOr(false)
            ->setType(Navigation\Type::Range_Date)
            ->setRange(true)
            ->setSort(self::$SORT)
            ->setMetadata(array(self::$METADATA))
            ->setRefinements(array(self::$REFINEMENT_RANGE, self::$REFINEMENT_VALUE))
            ->setMoreRefinements(true);

        self::$REFINEMENT_MATCH_VALUE = new RefinementMatch\Value();
        self::$REFINEMENT_MATCH_VALUE->setValue('adverb')
            ->setCount(43);

        self::$REFINEMENT_MATCH = new RefinementMatch();
        self::$REFINEMENT_MATCH->setName('grapheme')
            ->setValues(array(self::$REFINEMENT_MATCH_VALUE));

        self::$RECORD = new Record();
        self::$RECORD->setId("fw90314jh289t")
            ->setTitle("Periwinkle")
            ->setSnippet("Curator")
            ->setUrl("exemplar.com")
            ->setAllMeta(array(
                "look" => "at",
                "all" => "my",
                "keys" => array("we", "are", "the", "values")
            ))
            ->setRefinementMatches(array(self::$REFINEMENT_MATCH));

        self::$CONTENT_ZONE = new ContentZone();
        self::$CONTENT_ZONE->setId("23425n89hr")
            ->setName("porcelain")
            ->setContent("mushy");

        self::$BANNER_ZONE = new BannerZone();
        self::$BANNER_ZONE->setId("asf0j2380jf")
            ->setName("vitruvian")
            ->setBannerUrl("man");

        self::$RICH_CONTENT_ZONE = new RichContentZone();
        self::$RICH_CONTENT_ZONE->setId("f90j1e1rf")
            ->setName("appalled")
            ->setRichContent("crestfallen");

        self::$RECORD_ZONE = new RecordZone();
        self::$RECORD_ZONE->setId("1240jfw9s8")
            ->setName("gorbachev")
            ->setQuery("searchTerms")
            ->setRecords(array(self::$RECORD));

        self::$TEMPLATE = new Template();
        self::$TEMPLATE->setId("fad87g114")
            ->setName("bulbous")
            ->setRuleName("carmageddon")
            ->setZones(array(
                "content_zone" => self::$CONTENT_ZONE,
                "record_zone" => self::$RECORD_ZONE
            ));

        self::$CUSTOM_URL_PARAM = new CustomUrlParam();
        self::$CUSTOM_URL_PARAM->setKey("guava")->setValue("mango");

        self::$RESTRICT_NAVIGATION = new RestrictNavigation();
        self::$RESTRICT_NAVIGATION->setCount(2)
            ->setName("categories");

        self::$PARTIAL_MATCH_RULE = new PartialMatchRule();
        self::$PARTIAL_MATCH_RULE->setMustMatch(4)
            ->setTerms(2)
            ->setTermsGreaterThan(45)
            ->setPercentage(true);

        self::$MATCH_STRATEGY = new MatchStrategy();
        self::$MATCH_STRATEGY->setRules(array(self::$PARTIAL_MATCH_RULE));

        self::$REQUEST = new Request();
        self::$REQUEST->clientKey = "adf7h8er7h2r";
        self::$REQUEST->collection = "ducks";
        self::$REQUEST->area = "surface";
        self::$REQUEST->skip = 12;
        self::$REQUEST->pageSize = 30;
        self::$REQUEST->biasingProfile = "ballooning";
        self::$REQUEST->language = "en";
        self::$REQUEST->pruneRefinements = true;
        self::$REQUEST->returnBinary = false;
        self::$REQUEST->query = "cantaloupe";
        self::$REQUEST->refinementQuery = "cranberry";
        self::$REQUEST->sort = array(self::$SORT);
        self::$REQUEST->fields = array("pineapple", "grape", "clementine");
        self::$REQUEST->orFields = array("pumpernickel", "rye");
        self::$REQUEST->refinements = array(self::$REFINEMENT_RANGE, self::$REFINEMENT_VALUE);
        self::$REQUEST->customUrlParams = array(self::$CUSTOM_URL_PARAM);
        self::$REQUEST->wildcardSearchEnabled = true;
        self::$REQUEST->restrictNavigation = self::$RESTRICT_NAVIGATION;
        self::$REQUEST->matchStrategy = self::$MATCH_STRATEGY;

        self::$REFINEMENTS_REQUEST = new RefinementsRequest();
        self::$REFINEMENTS_REQUEST->originalQuery = self::$REQUEST;
        self::$REFINEMENTS_REQUEST->navigationName = "height";

        self::$RESULTS = new Results();
        self::$RESULTS->setArea("christmas");
        self::$RESULTS->setBiasingProfile("unbiased");
        self::$RESULTS->setClusters(array(Object::$CLUSTER));
        self::$RESULTS->setAvailableNavigation(array(Object::$NAVIGATION));
        self::$RESULTS->setDidYouMean(array("square", "skewer"));
        self::$RESULTS->setErrors("criminey!");
        self::$RESULTS->setPageInfo(Object::$PAGE_INFO);
        self::$RESULTS->setQuery("skwuare");
        self::$RESULTS->setOriginalQuery("skwuare---");
        self::$RESULTS->setCorrectedQuery("square");
        self::$RESULTS->setRecords(array(Object::$RECORD));
        self::$RESULTS->setRedirect("/to/the/moon.html");
        self::$RESULTS->setSelectedNavigation(array(Object::$NAVIGATION));
        self::$RESULTS->setTemplate(Object::$TEMPLATE);
        self::$RESULTS->setSiteParams(array(Object::$METADATA));
        self::$RESULTS->setRelatedQueries(array("squawk", "ask"));
        self::$RESULTS->setRewrites(array("Synonym", "Antonym", "Homonym"));
        self::$RESULTS->setTotalRecordCount(34);

        self::$REFINEMENTS_RESULT = new RefinementsResult();
        self::$REFINEMENTS_RESULT->setErrors("Could not load");
        self::$REFINEMENTS_RESULT->setNavigation(Object::$NAVIGATION);
    }
}

Object::init();
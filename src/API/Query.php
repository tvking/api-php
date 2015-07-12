<?php

namespace GroupByInc\API;

use GroupByInc\API\Model\CustomUrlParam;
use GroupByInc\API\Model\MatchStrategy as MMatchStrategy;
use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\PartialMatchRule as MPartialMatchRule;
use GroupByInc\API\Model\Refinement;
use GroupByInc\API\Model\Refinement\Type;
use GroupByInc\API\Model\RefinementRange;
use GroupByInc\API\Model\RefinementValue;
use GroupByInc\API\Model\Sort as MSort;
use GroupByInc\API\Request\MatchStrategy as RMatchStrategy;
use GroupByInc\API\Request\PartialMatchRule as RPartialMatchRule;
use GroupByInc\API\Request\RefinementsRequest;
use GroupByInc\API\Request\Request;
use GroupByInc\API\Request\RestrictNavigation;
use GroupByInc\API\Request\SelectedRefinement;
use GroupByInc\API\Request\SelectedRefinementRange;
use GroupByInc\API\Request\SelectedRefinementValue;
use GroupByInc\API\Request\Sort as RSort;
use GroupByInc\API\Util\SerializerFactory;
use GroupByInc\API\Util\StringBuilder;
use GroupByInc\API\Util\StringUtils;
use JMS\Serializer\Serializer;
use RuntimeException;

class Symbol
{
    const TILDE = "~";
    const DOT = ".";
    const DOUBLE_DOT = "..";
    const EQUAL = "=";
    const COLON = ":";
    const AMPERSAND = "&";
    const SLASH = "/";
}

class Query
{
    /** @var string */
    private $query;
    /** @var int */
    private $skip = 0;
    /** @var int */
    private $pageSize = 10;
    /** @var string */
    private $collection;
    /** @var string */
    private $area;
    /** @var string */
    private $biasingProfile;
    /** @var string */
    private $language;
    /** @var MSort[] */
    private $sort;
    /** @var CustomUrlParam[] */
    private $customUrlParams = array();
    /** @var Navigation[] */
    private $navigations = array();
    /** @var string[] */
    private $fields = array();
    /** @var string[] */
    private $orFields = array();
    /** @var bool */
    private $pruneRefinements = true;
    /** @var bool */
    private $disableAutocorrection = false;
    /** @var bool */
    private $wildcardSearchEnabled = false;
    // Removed until CBOR support for serialization / de-serialization improves
//    /** @var bool */
//    private $returnBinary = false;
    /** @var RestrictNavigation */
    private $restrictNavigation;

    /** @var Serializer */
    private $serializer;

    const TILDE_REGEX = "/~((?=[\\w]*[=:]))/";

    /**
     * @param mixed $request
     *
     * @return string
     */
    private function requestToJson($request)
    {
        $jsonRequest = null;
        try {
            $jsonRequest = $this->serializer->serialize($request, 'json');
        } catch (RuntimeException $e) {
            throw new RuntimeException('Unable to serialize request ' . var_dump($request));
        }

        return $jsonRequest;
    }

    /**
     * @param string $clientKey Your client key.
     *
     * @return string JSON representation of request to Bridge.
     */
    public function getBridgeJson($clientKey)
    {
        $data = $this->populateRequest($clientKey);
        return $this->requestToJson($data);
    }

    /**
     * @param string $clientKey      Your client key.
     * @param string $navigationName Name of the navigation to get refinements for
     *
     * @return string JSON representation of request to Bridge.
     */
    public function getBridgeRefinementsJson($clientKey, $navigationName)
    {
        $data = new RefinementsRequest();
        $data->originalQuery = $this->populateRequest($clientKey);
        $data->navigationName = $navigationName;
        return $this->requestToJson($data);
    }

    /**
     * @param string $clientKey
     *
     * @return Request
     */
    private function populateRequest($clientKey)
    {
        $request = new Request();
        $request->clientKey = $clientKey;
        $request->area = $this->area;
        $request->collection = $this->collection;
        $request->query = $this->query;
        $request->fields = $this->fields;
        $request->orFields = $this->orFields;
        $request->language = $this->language;
        $request->biasingProfile = $this->biasingProfile;
        $request->pageSize = $this->pageSize;
        $request->skip = $this->skip;
        $request->customUrlParams = $this->customUrlParams;
        $request->refinements = $this->generateSelectedRefinements($this->navigations);
        $request->restrictNavigation = $this->restrictNavigation;

        $pruneRefinements = $this->pruneRefinements;
        if (isset($pruneRefinements) && $pruneRefinements === false) {
            $request->pruneRefinements = false;
        }

        $disableAutocorrection = $this->disableAutocorrection;
        if (isset($disableAutocorrection) && $disableAutocorrection === true) {
            $request->disableAutocorrection = true;
        }

        $wildcardSearchEnabled = $this->wildcardSearchEnabled;
        if (isset($wildcardSearchEnabled) && $wildcardSearchEnabled === true) {
            $request->wildcardSearchEnabled = true;
        }

        if (!empty($this->sort)) {
            foreach ($this->sort as $s) {
                array_push($request->sort, $this->convertSort($s));
            }
        }

//        $returnBinary = $this->returnBinary;
//        if (isset($returnBinary) && $returnBinary === true) {
//            $request->returnBinary = true;
//        }

        return $request;
    }

    /**
     * @param Navigation[] $navigations
     *
     * @return Refinement[]
     */
    private function generateSelectedRefinements($navigations)
    {
        $refinements = [];
        foreach ($navigations as $key => $navigation) {
            foreach ($navigation->getRefinements() as $refinement) {
                switch ($refinement->getType()) {
                    case Type::Range: {
                        /** @var RefinementRange $rr */
                        $rr = $refinement;
                        $selectedRefinementRange = new SelectedRefinementRange();
                        $selectedRefinementRange
                            ->setNavigationName($navigation->getName())
                            ->setLow($rr->getLow())
                            ->setHigh($rr->getHigh())
                            ->setExclude($rr->isExclude());

                        array_push($refinements, $selectedRefinementRange);
                        break;
                    }
                    case Type::Value: {
                        /** @var RefinementValue $rv */
                        $rv = $refinement;
                        $selectedRefinementValue = new SelectedRefinementValue();
                        $selectedRefinementValue
                            ->setNavigationName($navigation->getName())
                            ->setValue($rv->getValue())
                            ->setExclude($rv->isExclude());

                        array_push($refinements, $selectedRefinementValue);
                        break;
                    }
                }
            }
        }
        return $refinements;
    }

    /**
     * @param string $clientKey Your client key.
     *
     * @return string JSON representation of request to Bridge.
     */
    public function getBridgeJsonRefinementSearch($clientKey)
    {
        $data = new Request();
        $data->clientKey = $clientKey;
        $data->collection = $this->collection;
        $data->area = $this->area;
        $data->refinementQuery = $this->query;

        $wildcardSearchEnabled = $this->wildcardSearchEnabled;
        if (isset($wildcardSearchEnabled) && $wildcardSearchEnabled === true) {
            $data->wildcardSearchEnabled = true;
        }

        return $this->requestToJson($data);
    }

    public function __construct()
    {
        $this->serializer = SerializerFactory::build();
    }

    /**
     * @return string The current search string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query The search string.
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return string The data sub-collection.
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param string $collection The string representation of a collection query.
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return string The area name.
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param string $area The area name.
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * @return string[] A list of metadata fields that will be returned by the search engine.
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return string[] A list of the fields that the search service will treat as OR'able.
     */
    public function getOrFields()
    {
        return $this->orFields;
    }

    /**
     * @param string[] $fields A list of case-sensitive names of the attributes to return.
     */
    public function addFields($fields)
    {
        $this->fields = array_merge($this->fields, $fields);
    }

    /**
     * @return Navigation[]
     */
    public function &getNavigations()
    {
        return $this->navigations;
    }

    /**
     * @param Navigation[] $navigations
     */
    public function setNavigations($navigations)
    {
        $this->navigations = $navigations;
    }

    /**
     * @param string $name The case-sensitive name of the attribute to return.
     */
    public function addField($name)
    {
        array_push($this->fields, $name);
    }

    /**
     * @param string $name Field that should be treated as OR.
     */
    public function addOrField($name)
    {
        array_push($this->orFields, $name);
    }

    /**
     * @param string[] $fields A list of fields that should be treated as OR.
     */
    public function addOrFields($fields)
    {
        $this->orFields = array_merge($this->orFields, $fields);
    }

    /**
     * @param string $name  The parameter name.
     * @param string $value The parameter value.
     */
    public function addCustomUrlParamByName($name, $value)
    {
        $param = new CustomUrlParam();
        $this->addCustomUrlParam($param->setKey($name)->setValue($value));
    }

    /**
     * @param CustomUrlParam $param Set an additional parameter that can be used to trigger rules.
     */
    public function addCustomUrlParam($param)
    {
        array_push($this->customUrlParams, $param);
    }

    public function splitRefinements($refinementString)
    {
        if (StringUtils::isNotBlank($refinementString)) {
            return preg_split(self::TILDE_REGEX, $refinementString, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        }
        return [];
    }

    /**
     * @param string $refinementString A tilde separated list of refinements.
     */
    public function addRefinementsByString($refinementString)
    {
        if ($refinementString == null) {
            return;
        }

        $refinementStrings = self::splitRefinements($refinementString);
        foreach ($refinementStrings as $refinementString) {
            if (empty($refinementString) || "=" == $refinementString) {
                continue;
            }
            $colon = strpos($refinementString, Symbol::COLON);
            $equals = strpos($refinementString, Symbol::EQUAL);
            //when === false, it means it did not find the substring in the string
            $isRange = !($colon === false) && ($equals === false);

            $refinement = null;
            if ($isRange) {
                $nameValue = explode(Symbol::COLON, $refinementString, 2);
                $refinement = new RefinementRange();
                if (StringUtils::endsWith($nameValue[1], Symbol::DOUBLE_DOT)) {
                    $value = explode(Symbol::DOUBLE_DOT, $nameValue[1]);
                    $refinement->setLow($value[0]);
                    $refinement->setHigh("");
                } else if (StringUtils::startsWith($nameValue[1], Symbol::DOUBLE_DOT)) {
                    $refinement->setLow("");
                    $value = explode(Symbol::DOUBLE_DOT, $nameValue[1]);
                    $refinement->setHigh($value[1]);
                } else {
                    $lowHigh = explode(Symbol::DOUBLE_DOT, $nameValue[1]);
                    $refinement->setLow($lowHigh[0]);
                    $refinement->setHigh($lowHigh[1]);
                }
            } else {
                $nameValue = explode(Symbol::EQUAL, $refinementString, 2);
                $refinement = new RefinementValue();
                $refinement->setValue($nameValue[1]);
            }
            if (!empty($nameValue[0])) {
                $this->addRefinement($nameValue[0], $refinement);
            }
        }
    }

    /**
     * @param string     $navigationName The name of the Navigation.
     * @param Refinement $refinement     A RefinementRange or RefinementValue object.
     */
    public function addRefinement($navigationName, $refinement)
    {
        $navigation = null;
        if (array_key_exists($navigationName, $this->navigations)) {
            $navigation = $this->navigations[$navigationName];
        } else {
            $navigation = new Navigation();
            $navigation->setName($navigationName)->setRange($refinement instanceof SelectedRefinementRange);
            $this->navigations[$navigationName] = $navigation;
        }
        $refinements = $navigation->getRefinements();
        array_push($refinements, $refinement);
        $navigation->setRefinements($refinements);
    }

    /**
     * @param string $navigationName The name of the refinement.
     * @param mixed  $low            The low value.
     * @param mixed  $high           The high value.
     * @param bool   $exclude        True if the results should exclude this range refinement, false otherwise.
     */
    public function addRangeRefinement($navigationName, $low, $high, $exclude = false)
    {
        $refinement = new RefinementRange();
        $this->addRefinement($navigationName, $refinement->setLow($low)->setHigh($high)->setExclude($exclude));
    }

    /**
     * @param string $navigationName The name of the refinement.
     * @param mixed  $value          The refinement value.
     * @param bool   $exclude        True if the results should exclude this value refinement, false otherwise.
     */
    public function addValueRefinement($navigationName, $value, $exclude = false)
    {
        $refinement = new RefinementValue();;
        $this->addRefinement($navigationName, $refinement->setValue($value)->setExclude($exclude));
    }

    /**
     * @return bool Are refinements with zero counts being removed.
     */
    public function isPruneRefinements()
    {
        return $this->pruneRefinements;
    }

    /**
     * @param bool $pruneRefinements Specifies whether refinements should be pruned.
     */
    public function setPruneRefinements($pruneRefinements)
    {
        $this->pruneRefinements = $pruneRefinements;
    }

    /**
     * @return MSort[] The current list of sort parameters.
     */
    public function &getSort()
    {
        return $this->sort;
    }

    /**
     * @param MSort[] $sort Any number of sort criteria.
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return int The number of documents to skip.
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * @param int $skip The number of documents to skip.
     */
    public function setSkip($skip)
    {
        $this->skip = $skip;
    }

    /**
     * @return CustomUrlParam[] A list of custom url params.
     */
    public function getCustomUrlParams()
    {
        return $this->customUrlParams;
    }

    /**
     * @param CustomUrlParam[] $customUrlParams Set the custom url params.
     */
    public function setCustomUrlParams($customUrlParams)
    {
        $this->customUrlParams = $customUrlParams;
    }

//    /**
//     * @return bool Is return JSON set to true.
//     */
//    public function isReturnBinary()
//    {
//        return $this->returnBinary;
//    }
//
//    /**
//     * @param bool $returnBinary Whether to tell the bridge to return binary data rather than JSON.
//     */
//    public function setReturnBinary($returnBinary)
//    {
//        $this->returnBinary = $returnBinary;
//    }

    /**
     * @return string The current language restrict value.
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language The value for language restrict.
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string The current biasing profile name.
     */
    public function getBiasingProfile()
    {
        return $this->biasingProfile;
    }

    /**
     * @param string $biasingProfile Override the biasing profile used for this query.
     */
    public function setBiasingProfile($biasingProfile)
    {
        $this->biasingProfile = $biasingProfile;
    }

    /**
     * @return int The current page size.
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize The number of records to return with the query.
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @return boolean
     */
    public function isDisableAutocorrection()
    {
        return $this->disableAutocorrection;
    }

    /**
     * @param boolean $disableAutocorrection Specifies whether the auto-correction behavior should be disabled.
     *                                       By default, when no results are returned for the given query (and there is
     *                                       a did-you-mean available), the first did-you-mean is automatically queried
     *                                       instead.
     */
    public function setDisableAutocorrection($disableAutocorrection)
    {
        $this->disableAutocorrection = $disableAutocorrection;
    }

    /**
     * @return boolean
     */
    public function isWildcardSearchEnabled()
    {
        return $this->wildcardSearchEnabled;
    }

    /**
     * @param boolean $wildcardSearchEnabled Indicate if the *(star) character in the search string should be treated
     *                                       as a wildcard prefix search. For example, `sta*` will match `star` and
     *                                       `start`.
     */
    public function setWildcardSearchEnabled($wildcardSearchEnabled)
    {
        $this->wildcardSearchEnabled = $wildcardSearchEnabled;
    }

    /**
     * <b>Warning</b>  This will count as two queries against your search index.
     *
     * Typically, this feature is used when you have a large number of navigation items that will overwhelm the end
     * user. It works by using one of the existing navigation items to decide what the query is about and fires a second
     * query to restrict the navigation to the most relevant set of navigation items for this search term.
     *
     * For example, if you pass in a search of `paper` and a restrict navigation of `category:2`
     *
     * The bridge will find the category navigation refinements in the first query and fire a second query for the top 2
     * most populous categories.  Therefore, a search for something generic like "paper" will bring back top category
     * matches like copy paper (1,030), paper pads (567).  The bridge will fire off the second query with the search
     * term, plus an OR refinement with the most likely categories.  The navigation items in the first query are
     * entirely replaced with the navigation items in the second query, except for the navigation that was used for the
     * restriction so that users still have the ability to navigate by all category types.
     *
     * @param RestrictNavigation $restrictNavigation Restriction criteria
     */
    public function setRestrictNavigation($restrictNavigation)
    {
        $this->restrictNavigation = $restrictNavigation;
    }

    /** @return RestrictNavigation */
    public function getRestrictNavigation()
    {
        return $this->restrictNavigation;
    }

    /**
     * @return string A string representation of all of the currently set refinements.
     */
    public function getRefinementString()
    {
        if (!empty($this->navigations)) {
            $builder = new StringBuilder();
            foreach ($this->navigations as $n) {
                foreach ($n->getRefinements() as $r) {
                    $builder->append(Symbol::TILDE)->append($n->getName())->append($r->toTildeString());
                }
            }
            if ($builder->length() > 0) {
                return $builder->__toString();
            }
        }
        return null;
    }

    /**
     * @return string A string representation of all of the currently set custom url parameters.
     */
    public function getCustomUrlParamsString()
    {
        if (!empty($this->customUrlParams)) {
            $builder = new StringBuilder();
            foreach ($this->customUrlParams as $c) {
                $builder->append(Symbol::TILDE)->append($c->getKey())->append(Symbol::EQUAL)->append($c->getValue());
            }
            if ($builder->length() > 0) {
                return $builder->__toString();
            }
        }
        return null;
    }

    /**
     * @param MSort $sort
     *
     * @return RSort
     */
    protected static function convertSort($sort)
    {
        /** @var RSort $convertedSort */
        $convertedSort = null;
        if (!empty($sort)) {
            $convertedSort = new RSort();
            $convertedSort->setField($sort->getField());
            switch ($sort->getOrder()) {
                case MSort\Order::Ascending:
                    $convertedSort->setOrder(RSort\Order::Ascending);
                    break;
                case MSort\Order::Descending:
                    $convertedSort->setOrder(RSort\Order::Descending);
                    break;
            }
        }
        return $convertedSort;
    }

    /**
     * @param MMatchStrategy $strategy
     *
     * @return RMatchStrategy
     */
    protected static function convertPartialMatchStrategy($strategy)
    {
        /** @var RMatchStrategy $convertedStrategy */
        $convertedStrategy = null;
        if (!empty($strategy)) {
            $rules = $strategy->getRules();
            if (!empty($rules)) {
                $convertedStrategy = new RMatchStrategy();
                /** @var MPartialMatchRule $r */
                foreach ($rules as $r) {
                    array_push($rules, Query::convertPartialMatchRule($r));
                }
                $strategy->setRules($rules);
            }
        }
        return $convertedStrategy;
    }

    /**
     * @param MPartialMatchRule $rule
     *
     * @return RPartialMatchRule
     */
    protected static function convertPartialMatchRule($rule)
    {
        /** @var RPartialMatchRule $convertedRule */
        $convertedRule = null;
        if (!empty($rule)) {
            $convertedRule = new RPartialMatchRule();
            $convertedRule->setTerms($rule->getTerms())
                ->setTermsGreaterThan($rule->getTermsGreaterThan())
                ->setMustMatch($rule->getMustMatch())
                ->setPercentage($rule->isPercentage());
        }
        return $convertedRule;
    }

}

<?php

namespace GroupByInc\API;

use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\Refinement;
use GroupByInc\API\Model\Refinement\Type;
use GroupByInc\API\Model\RefinementRange;
use GroupByInc\API\Model\RefinementValue;
use GroupByInc\API\Model\SelectedRefinement;
use GroupByInc\API\Model\SelectedRefinementRange;
use GroupByInc\API\Model\SelectedRefinementValue;
use GroupByInc\API\Request\CustomUrlParam;
use GroupByInc\API\Request\Request;
use GroupByInc\API\Request\Sort;
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
    /** @var Sort */
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

    /** @var Serializer */
    private $serializer;

    /**
     * @param Request $request
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
     * @return string JSON representation of request to Bridge.
     */
    public function getBridgeJson($clientKey)
    {
        $data = new Request();
        $data->clientKey = $clientKey;
        $data->area = $this->area;
        $data->collection = $this->collection;
        $data->query = $this->query;
        $data->sort = $this->sort;
        $data->fields = $this->fields;
        $data->orFields = $this->orFields;
        $data->language = $this->language;
        $data->biasingProfile = $this->biasingProfile;
        $data->pageSize = $this->pageSize;
        $data->skip = $this->skip;
        $data->customUrlParams = $this->customUrlParams;

        /** @var SelectedRefinement[] $refinements */
        $refinements = [];
        foreach ($this->navigations as $key => $navigation) {
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
        $data->refinements = $refinements;

        $pruneRefinements = $this->pruneRefinements;
        if (isset($pruneRefinements) && $pruneRefinements === false) {
            $data->pruneRefinements = false;
        }

        $disableAutocorrection = $this->disableAutocorrection;
        if (isset($disableAutocorrection) && $disableAutocorrection === true) {
            $data->disableAutocorrection = true;
        }

        $wildcardSearchEnabled = $this->wildcardSearchEnabled;
        if (isset($wildcardSearchEnabled) && $wildcardSearchEnabled === true) {
            $data->wildcardSearchEnabled = true;
        }

//        $returnBinary = $this->returnBinary;
//        if (isset($returnBinary) && $returnBinary === true) {
//            $data->returnBinary = true;
//        }

        return $this->requestToJson($data);
    }

    /**
     * @param string $clientKey Your client key.
     * @return string JSON representation of request to Bridge.
     */
    public function getBridgeJsonRefinementSearch($clientKey)
    {
        $data = new Request();
        $data->clientKey = $clientKey;
        $data->collection = $this->collection;
        $data->area = $this->area;
        $data->refinementSearch = $this->query;

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
     * @param string $field The field to sort on.
     * @param int $order The sort order.
     */
    public function setSortByField($field, $order)
    {
        $sort = new Sort();
        $this->setSort($sort->setField($field)->setOrder($order));
    }

    /**
     * @param string $name The parameter name.
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

    /**
     * @param string $refinementString A tilde separated list of refinements.
     */
    public function addRefinementsByString($refinementString)
    {
        if ($refinementString == null) {
            return;
        }

        $refinementStrings = explode(Symbol::TILDE, $refinementString);
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
                    //starts with ..
                } else if (substr($nameValue[1], 0, 2) == Symbol::DOUBLE_DOT) {
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
     * @param string $navigationName The name of the Navigation.
     * @param Refinement $refinement A RefinementRange or RefinementValue object.
     */
    public function addRefinement($navigationName, $refinement)
    {
        $navigation = null;
        if (array_key_exists($navigationName, $this->navigations)) {
            $navigation = $this->navigations[$navigationName];
        } else if ($navigation == null) {
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
     * @param mixed $low The low value.
     * @param mixed $high The high value.
     * @param bool $exclude True if the results should exclude this range refinement, false otherwise.
     */
    public function addRangeRefinement($navigationName, $low, $high, $exclude = false)
    {
        $refinement = new RefinementRange();
        $this->addRefinement($navigationName, $refinement->setLow($low)->setHigh($high)->setExclude($exclude));
    }

    /**
     * @param string $navigationName The name of the refinement.
     * @param mixed $value The refinement value.
     * @param bool $exclude True if the results should exclude this value refinement, false otherwise.
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
     * @return Sort The current sort parameter.
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param Sort $sort A Sort object representing the field and order.
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
     * By default, when no results are returned for the given query (and there is a did-you-mean available),
     * the first did-you-mean is automatically queried instead.
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
     * as a wildcard prefix search. For example, `sta*` will match `star` and `start`.
     */
    public function setWildcardSearchEnabled($wildcardSearchEnabled)
    {
        $this->wildcardSearchEnabled = $wildcardSearchEnabled;
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

}

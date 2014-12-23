<?php

namespace GroupByInc\API;

use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\Refinement;
use GroupByInc\API\Model\RefinementRange;
use GroupByInc\API\Model\RefinementValue;
use GroupByInc\API\Request\CustomUrlParam;
use GroupByInc\API\Request\Request;
use GroupByInc\API\Request\Sort;
use GroupByInc\API\Util\StringBuilder;
use GroupByInc\API\Util\StringUtils;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
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
    private $sort;
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
    // Removed until CBOR support for serialization / deserialization improves
//    /** @var bool */
//    private $returnBinary = false;
    /** @var bool */
    private $pruneRefinements = true;

    /** @var CustomUrlParam[] */
    private $customUrlParams = array();
    /** @var Navigation[] */
    private $navigations = array();
    /** @var string[] */
    private $fields = array();
    /** @var string[] */
    private $orFields = array();
    /** @var Serializer */
    private $serializer;

    /** @var CompressResponse */
    private $compressResponse = false;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()->build();
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
            $navigation->setName($navigationName)->setRange($refinement instanceof RefinementRange);
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
     */
    public function addRangeRefinement($navigationName, $low, $high)
    {
        $refinement = new RefinementRange();
        $this->addRefinement($navigationName, $refinement->setLow($low)->setHigh($high));
    }

    /**
     * @param string $navigationName The name of the refinement.
     * @param mixed $value The refinement value.
     */
    public function addValueRefinement($navigationName, $value)
    {
        $refinement = new RefinementValue();;
        $this->addRefinement($navigationName, $refinement->setValue($value));
    }

    /**
     * @param string $clientKey
     * @return string JSON representation of request to Bridge.
     */
    public function getBridgeJson($clientKey)
    {
        $data = new Request();
        $data->clientKey = $clientKey;
        $data->collection = $this->collection;
        $data->area = $this->area;
        $data->query = $this->query;
        $data->navigations = $this->navigations;
        $data->sort = $this->sort;
        $data->fields = $this->fields;
        $data->orFields = $this->orFields;
        $data->biasingProfile = $this->biasingProfile;
        $data->pageSize = $this->pageSize;
//        $data->returnBinary = $this->returnBinary;
        $data->skip = $this->skip;
        $data->customUrlParams = $this->customUrlParams;
        $data->language = $this->language;

        $pruneRefinements = $this->pruneRefinements;
        if (isset($pruneRefinements) && $pruneRefinements === true) {
            $data->pruneRefinements = true;
        }

        $jsonRequest = null;
        try {
            $jsonRequest = $this->serializer->serialize($data, 'json');
        } catch (RuntimeException $e) {
            // probably should do something here
        }

        return $jsonRequest;
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
     * @return CompressResponse
     */
    public function getCompressResponse()
    {
        return $this->compressResponse;
    }

    /**
     * @param $pCompressResponse
     */
    public function setCompressResponse($pCompressResponse)
    {
        $this->compressResponse = $pCompressResponse;
    }

}

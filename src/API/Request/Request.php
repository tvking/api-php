<?php

namespace GroupByInc\API\Request;

use GroupByInc\API\Model\CustomUrlParam;
use GroupByInc\API\Model\Navigation;

class Request
{
    /** @var string */
    public $clientKey;
    /** @var string */
    public $userId;
    /** @var string */
    public $collection;
    /** @var string */
    public $area;
    /** @var string */
    public $biasingProfile;
    /** @var string */
    public $language;
    /** @var string */
    public $query;
    /** @var string */
    public $refinementQuery;
    /** @var RestrictNavigation */
    public $restrictNavigation;
    /** @var MatchStrategy */
    public $matchStrategy;
    /** @var Biasing */
    public $biasing;

    /** @var Sort[] */
    public $sort = array();
    /** @var string[] */
    public $fields = array();
    /** @var string[] */
    public $orFields = array();
    /** @var SelectedRefinement[] */
    public $refinements = array();
    /** @var CustomUrlParam[] */
    public $customUrlParams = array();
    
    /** @var string[] */
    public $includedNavigations;
    /** @var string[] */
    public $excludedNavigations;

    /** @var int */
    public $skip;
    /** @var int */
    public $pageSize;
    /** @var bool */
    public $returnBinary;
    /** @var bool */
    public $disableAutocorrection;
    /** @var bool */
    public $pruneRefinements = true;
    /** @var bool */
    public $wildcardSearchEnabled = false;
}
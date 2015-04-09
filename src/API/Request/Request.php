<?php

namespace GroupByInc\API\Request;

use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\RestrictNavigation;
use GroupByInc\API\Model\SelectedRefinement;

class Request
{
    /** @var string */
    public $clientKey;
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
    /** @var Sort */
    public $sort;

    /** @var string[] */
    public $fields = array();
    /** @var string[] */
    public $orFields = array();
    /** @var SelectedRefinement[] */
    public $refinements = array();
    /** @var CustomUrlParam[] */
    public $customUrlParams = array();

    /** @var int */
    public $skip;
    /** @var int */
    public $pageSize;
    /** @var bool */
    public $returnBinary;
    /** @var bool */
    public $disableAutocorrection;
    /** @var bool */
    public $pruneRefinements;
    /** @var bool */
    public $wildcardSearchEnabled;
    /** @var RestrictNavigation */
    public $restrictNavigation;
}
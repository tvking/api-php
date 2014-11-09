<?php

namespace GroupByInc\API\Request;

use GroupByInc\API\Model\Navigation;

class Request
{
    /** @var string */
    public $clientKey;
    /** @var string */
    public $collection;
    /** @var string */
    public $area;
    /** @var int */
    public $skip;
    /** @var int */
    public $pageSize;
    /** @var string */
    public $biasingProfile;
    /** @var string */
    public $language;
    /** @var bool */
    public $pruneRefinements;
    /** @var bool */
    public $returnBinary;
    /** @var string */
    public $query;
    /** @var Sort */
    public $sort;
    /** @var string[] */
    public $fields = array();
    /** @var string[] */
    public $orFields = array();
    /** @var Navigation[] */
    public $navigations = array();
    /** @var CustomUrlParam[] */
    public $customUrlParams = array();
}
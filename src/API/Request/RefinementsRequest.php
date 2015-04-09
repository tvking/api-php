<?php

namespace GroupByInc\API\Request;

use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\RestrictNavigation;
use GroupByInc\API\Model\SelectedRefinement;

class RefinementsRequest
{
    /** @var Request */
    public $originalQuery;
    /** @var string */
    public $navigationName;
}
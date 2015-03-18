<?php

namespace GroupByInc\API\Model;

use JMS\Serializer\Annotation as JMS;

class Results
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $totalRecordCount;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $area;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $biasingProfile;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $redirect;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $errors;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $query;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $originalQuery;
    /**
     * @var string
     * @JMS\Type("string")
     */
    public $correctedQuery;

    /**
     * @var Template
     * @JMS\Type("GroupByInc\API\Model\Template")
     */
    public $template;
    /**
     * @var PageInfo
     * @JMS\Type("GroupByInc\API\Model\PageInfo")
     */
    public $pageInfo;
    /**
     * @var Navigation[]
     * @JMS\Type("array<GroupByInc\API\Model\Navigation>")
     */
    public $availableNavigation = array();
    /**
     * @var Navigation[]
     * @JMS\Type("array<GroupByInc\API\Model\Navigation>")
     */
    public $selectedNavigation = array();

    /**
     * @var Record[]
     * @JMS\Type("array<GroupByInc\API\Model\Record>")
     */
    public $records = array();
    /**
     * @var string[]
     * @JMS\Type("array<string>")
     */
    public $didYouMean = array();
    /**
     * @var string[]
     * @JMS\Type("array<string>")
     */
    public $relatedQueries = array();
    /**
     * @var string[]
     * @JMS\Type("array<string>")
     */
    public $rewrites = array();
    /**
     * @var Metadata[]
     * @JMS\Type("array<GroupByInc\API\Model\Metadata>")
     */
    public $siteParams = array();
    /**
     * @var Cluster[]
     * @JMS\Type("array<GroupByInc\API\Model\Cluster>")
     */
    public $clusters = array();

    /**
     * @return string[] A list of spell corrections based on the search term.
     */
    public function getDidYouMean()
    {
        return $this->didYouMean;
    }

    /**
     * @param string[] $didYouMean Set the list.
     */
    public function setDidYouMean($didYouMean)
    {
        $this->didYouMean = $didYouMean;
    }

    /**
     * @return string[] A list of related queries for a given search term.
     */
    public function getRelatedQueries()
    {
        return $this->relatedQueries;
    }

    /**
     * @param string[] $relatedQueries Set the related queries for a search term.
     */
    public function setRelatedQueries($relatedQueries)
    {
        $this->relatedQueries = $relatedQueries;
    }

    /**
     * @return Record[] A list of the records for this search and navigation state.
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param Record[] $records Set the records.
     */
    public function setRecords($records)
    {
        $this->records = $records;
    }

    /**
     * @return Template If a rule has fired, the associated template will be returned.
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Template $template Set the template.
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return PageInfo Information about the results page.
     */
    public function getPageInfo()
    {
        return $this->pageInfo;
    }

    /**
     * @param PageInfo $pageInfo Set the page info.
     */
    public function setPageInfo($pageInfo)
    {
        $this->pageInfo = $pageInfo;
    }

    /**
     * @return Navigation[] A list of all the ways in which you can filter the current result set.
     */
    public function getAvailableNavigation()
    {
        return $this->availableNavigation;
    }

    /**
     * @param Navigation[] $availableNavigation Set the available navigation.
     */
    public function setAvailableNavigation($availableNavigation)
    {
        $this->availableNavigation = $availableNavigation;
    }

    /**
     * @return Navigation[] A list of the currently selected navigations. Also known as breadcrumbs.
     */
    public function getSelectedNavigation()
    {
        return $this->selectedNavigation;
    }

    /**
     * @param Navigation[] $selectedNavigation Set the selected navigations.
     */
    public function setSelectedNavigation($selectedNavigation)
    {
        $this->selectedNavigation = $selectedNavigation;
    }

    /**
     * @return string A URL to redirect to based on this search term.
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @param string $redirect Set the redirect.
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

    /**
     * @return string representation of any errors encountered.
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $errors Set errors.
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return int A count of the total number of records in this search and navigation state.
     */
    public function getTotalRecordCount()
    {
        return $this->totalRecordCount;
    }

    /**
     * @param int $totalRecordCount Set the total record count.
     */
    public function setTotalRecordCount($totalRecordCount)
    {
        $this->totalRecordCount = $totalRecordCount;
    }

    /**
     * @return Cluster[] The list of clusters.
     */
    public function getClusters()
    {
        return $this->clusters;
    }

    /**
     * @param Cluster[] $clusters Set the search clusters.
     */
    public function setClusters($clusters)
    {
        $this->clusters = $clusters;
    }

    /**
     * @return Metadata[] A list of metadata as set in the area management section of the command center.
     */
    public function getSiteParams()
    {
        return $this->siteParams;
    }

    /**
     * @param Metadata[] $siteParams Set the site metadata.
     */
    public function setSiteParams($siteParams)
    {
        $this->siteParams = $siteParams;
    }

    /**
     * @return string The query sent to the bridge.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query Sets the query to the bridge.
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return string The original query sent to the search service.
     */
    public function getOriginalQuery()
    {
        return $this->originalQuery;
    }

    /**
     * @param string $originalQuery Sets the original query sent to the search service.
     */
    public function setOriginalQuery($originalQuery)
    {
        $this->originalQuery = $originalQuery;
    }

    /**
     * @return string The corrected query sent to the engine, if auto-correction is enabled.
     */
    public function getCorrectedQuery()
    {
        return $this->correctedQuery;
    }

    /**
     * @param string $correctedQuery Sets the corrected query sent to the engine, if auto-correction is enabled.
     */
    public function setCorrectedQuery($correctedQuery)
    {
        $this->correctedQuery = $correctedQuery;
    }

    /**
     * @return string[] A list of rewrites (spellings, synonyms, etc...) that occurred.
     */
    public function getRewrites()
    {
        return $this->rewrites;
    }

    /**
     * @param string[] $rewrites Sets the rewrites that occurred.
     */
    public function setRewrites($rewrites)
    {
        $this->rewrites = $rewrites;
    }

    /**
     * @return string The area that the search fired against.
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param string $area Sets the area.
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * @return string
     */
    public function getBiasingProfile()
    {
        return $this->biasingProfile;
    }

    /**
     * @param string $biasingProfile
     */
    public function setBiasingProfile($biasingProfile)
    {
        $this->biasingProfile = $biasingProfile;
    }
}


<?php

namespace GroupByInc\API\Url;

use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\Refinement\Type;
use GroupByInc\API\Model\SelectedRefinement;
use GroupByInc\API\Model\SelectedRefinementValue;
use GroupByInc\API\Query;
use GroupByInc\API\Symbol;
use GroupByInc\API\Util\ArrayUtils;
use GroupByInc\API\Util\StringBuilder;
use GroupByInc\API\Util\StringUtils;
use GroupByInc\API\Util\UriBuilder;
use RuntimeException;

class OperationType
{
    const Insert = 0;
    const Swap = 1;
}

class Beauty
{
    const PARAM_REPLACEMENT = "z";
    const SEARCH_NAVIGATION_NAME = "search";
    const REFINEMENTS_PARAM_DEFAULT = "refinements";
    const ID_PATTERN = "/(?:\\A|.*&)id=([^&]*).*/";
    const ID = "id";
    const VOWELS = "/[aoeuiAOEUIyY]/";
    const INSERT_INDICATOR = "i";
    const REPLACEMENT_DELIMITER = "-";
}

class UrlBeautifier
{
    /** @var UrlBeautifier[] */
    private static $beautifiers = array();
    /** @var Navigation */
    private static $searchNavigation;

    /** @var Navigation[] */
    private $remainingMappings = array();
    /** @var UrlReplacementRule[] */
    private $replacementRules = array();
    /** @var Navigation[] */
    private $nameToToken = array();
    /** @var Navigation[] */
    private $tokenToName = array();
    /** @var string */
    private $refinementsQueryParamName = Beauty::REFINEMENTS_PARAM_DEFAULT;
    /** @var string */
    private $append = null;

    static function init()
    {
        self::$searchNavigation = new Navigation();
    }

    /**
     * @param string $identifier The handle back to this UrlBeautifier.
     */
    public static function createUrlBeautifier($identifier)
    {
        self::$beautifiers[$identifier] = new UrlBeautifier();
    }

    /**
     * @return UrlBeautifier[] Get a map of UrlBeautifiers keyed by name.
     */
    public static function getUrlBeautifiers()
    {
        return self::$beautifiers;
    }

    /**
     * @param string $searchString The current search state.
     * @param string $existingRefinements The current refinement state.
     * @return string
     */
    public function toUrl($searchString, $existingRefinements)
    {
        $builder = new StringBuilder(Symbol::SLASH);
        $query = new Query();
        if (!empty($searchString)) {
            $query->setQuery($searchString);
        }
        $uri = new UriBuilder();
        $uri->setPath("");
        $query->addRefinementsByString($existingRefinements);
        $navigations = &$this->getDistinctRefinements($query);
        $this->addRefinements($query->getQuery(), $navigations, $builder, $uri);
        $this->addReferenceBlock($builder, $uri);
        $this->addAppend($uri);
        $this->addUnmappedRefinements($navigations, $uri);
        return $uri->build();
    }

    /**
     * @param Query $query
     * @return Navigation[]
     */
    private function &getDistinctRefinements(Query $query)
    {
        $navigations = &$query->getNavigations();
        foreach ($navigations as &$navigation) {
            $refinements = &$navigation->getRefinements();
            $names = array();
            for ($i = 0; $i < count($refinements); $i++) {
                $refinement = $refinements[$i];
                $name = $navigation->getName() . $refinement->toTildeString();
                if (!in_array($name, $names)) {
                    array_push($names, $name);
                } else {
                    unset($refinements[$i]);
                }
            }
        }
        return $navigations;
    }

    /**
     * @param string $searchString
     * @param Navigation[] $navigations
     * @param StringBuilder $pathLookup
     * @param UriBuilder $uri
     */
    private function addRefinements($searchString, array &$navigations, StringBuilder $pathLookup, UriBuilder $uri)
    {
        $indexOffset = strlen($uri->getPath()) + 1;
        /** @var UrlReplacement[] $replacements */
        $replacements = array();

        foreach ($this->remainingMappings as $m) {
            if ($m == self::$searchNavigation && !empty($searchString)) {
                $searchString = $this->applyReplacementRule($m, $searchString, $indexOffset, $replacements);
                $indexOffset += strlen($searchString) + 1;
                $this->addSearchString($searchString, $pathLookup, $uri);
                continue;
            }

            if (array_key_exists($m->getName(), $navigations)) {
                /** @var Navigation $n */
                $n = $navigations[$m->getName()];
                $refinements = $n->getRefinements();
                foreach ($refinements as $r) {
                    switch ($r->getType()) {
                        case Type::Value:
                            $pathLookup->append($this->getToken($n->getName()));
                            /** @var SelectedRefinementValue $valueRef */
                            $valueRef = $r;
                            $valueRef->setValue($this->applyReplacementRule($n, $valueRef->getValue(), $indexOffset, $replacements));
                            $encodedRefValue = Symbol::SLASH . urlencode($valueRef->getValue());
                            $indexOffset += strlen($valueRef->getValue()) + 1;
                            $uri->appendToPath($encodedRefValue);
                            ArrayUtils::remove($refinements, $r);
                            break;
                        case Type::Range:
                            error_log("You should not map ranges into URLs.");
                            continue 3;
                    }
                }
                if (empty($refinements)) {
                    unset($navigations[$m->getName()]);
                }
            }
        }
        if (!empty($replacements)) {
            $uri->setParameter(Beauty::PARAM_REPLACEMENT, UrlReplacement::buildQueryString($replacements));
        }
    }

    /**
     * @param Navigation $navigation
     * @param string $uri
     * @param int $indexOffset
     * @param UrlReplacement[] $replacements
     * @return string
     */
    private function applyReplacementRule(Navigation $navigation, $uri, $indexOffset, array &$replacements)
    {
        $builder = new StringBuilder($uri);
        foreach ($this->replacementRules as $replacementRule) {
            $replacementRule->apply($builder, $indexOffset, $navigation->getName(), $replacements);
        }
        return $builder->__toString();
    }

    /**
     * @param string $searchString
     * @param StringBuilder $reference
     * @param UriBuilder $uri
     */
    private function addSearchString($searchString, $reference, UriBuilder $uri)
    {
        if (!empty($searchString)) {
            $uri->appendToPath(Symbol::SLASH . urlencode($searchString));
            $reference->append(self::$searchNavigation->getDisplayName());
        }
    }

    /**
     * @param string $name
     * @return string
     */
    private function getToken($name)
    {
        $mapping = $this->nameToToken[$name];
        return $mapping == null ? null : $mapping->getDisplayName();
    }

    /**
     * @param string $reference
     * @param UriBuilder $uri
     */
    private function addReferenceBlock($reference, UriBuilder $uri)
    {
        if (strlen($reference) > 1) {
            $uri->appendToPath($reference);
        }
    }

    /**
     * @param UriBuilder $uri
     */
    private function addAppend(UriBuilder $uri)
    {
        if (!empty($this->append)) {
            $uri->appendToPath($this->append);
        }
    }

    /**
     * @param Navigation[] $navigations
     * @param UriBuilder $uri
     */
    private function addUnmappedRefinements(array $navigations, UriBuilder $uri)
    {
        if (!empty($navigations)) {
            $query = new Query();
            $distinctNavigations = &$this->getDistinctRefinements($query);
            foreach ($navigations as $nKey => $nValue) {
                if (array_key_exists($nKey, $distinctNavigations)) {
                    $n = $distinctNavigations[$nKey];
                    $n->setRefinements(array_merge($n->getRefinements(), $nValue->getRefinements()));
                } else {
                    $distinctNavigations[$nKey] = $nValue;
                }
            }
            $refinements = $query->getRefinementString();
            if (!empty($refinements)) {
                $uri->setParameter($this->refinementsQueryParamName, $refinements);
            }
        }
    }

    /**
     * @param string $uri
     * @param Query $default
     * @return Query
     * @throws RuntimeException
     */
    public function fromUrl($uri, Query $default = null)
    {
        $uriBuilder = new UriBuilder();
        $uriBuilder->setFromString($uri);
        $urlQueryString = $uriBuilder->getQuery();
        $matches = array();
        if (!empty($urlQueryString) && preg_match(Beauty::ID_PATTERN, $urlQueryString, $matches)) {
            $query = new Query();
            $query->addValueRefinement(Beauty::ID, $matches[1]);
            return $query;
        } else {
            $query = new Query();
            $replacementUrlQueryString = $this->getReplacementQuery($uriBuilder->getQuery());
            $pathSegments = array();
            $uriPath = $uriBuilder->getPath();
            if (!empty($this->append) && StringUtils::endsWith($uriPath, $this->append)) {
                $uriPath = substr($uriPath, 0, -strlen($this->append));
            }
            $pathSegments = array_merge($pathSegments, explode(Symbol::SLASH, $uriPath));
            $pathSegmentLookup = $this->lastSegment($pathSegments);
            if (count($pathSegments) > strlen($pathSegmentLookup)) {
                $this->removeUnusedPathSegments($pathSegments, $pathSegmentLookup);
            } else {
                return $default;
            }
            try {
                $pathSegments = $this->applyReplacementToPathSegment($pathSegments,
                    UrlReplacement::parseQueryString($replacementUrlQueryString));
            } catch (ParserException $e) {
                error_log("Replacement Query is malformed, returning default query");
                return $default;
            }
            while (count($pathSegments) > 0) {
                $this->addRefinement($pathSegments, $query, $pathSegmentLookup);
            }
            if (!empty($urlQueryString)) {
                $queryParams = explode("&", $urlQueryString);
                if ($queryParams != null && count($queryParams) > 0) {
                    foreach ($queryParams as $keyValue) {
                        if (StringUtils::startsWith($keyValue, $this->refinementsQueryParamName . Symbol::EQUAL)) {
                            $decodedKeyValue = urldecode($keyValue);
                            $kv = explode(Symbol::EQUAL, $decodedKeyValue, 2);
                            $query->addRefinementsByString($kv[1]);
                            break;
                        }
                    }
                }
            }
        }
        if ($query == null) {
            throw new RuntimeException("URL reference block is invalid, could not convert to query");
        }
        return $query;
    }

    /**
     * @param string $queryString
     * @return string
     */
    private function getReplacementQuery($queryString)
    {
        if (!empty($queryString)) {
            foreach (explode(Symbol::AMPERSAND, $queryString) as $token) {
                if (StringUtils::startsWith($token, Beauty::PARAM_REPLACEMENT . Symbol::EQUAL)) {
                    return urldecode(substr($token, 2));
                }
            }
        }
        return "";
    }

    /**
     * @param string[] $pathSegments
     * @return string
     */
    private function lastSegment(array &$pathSegments)
    {
        $lastSegment = $pathSegments[count($pathSegments) - 1];
        ArrayUtils::remove($pathSegments, $lastSegment);
        return $lastSegment;
    }

    private function removeUnusedPathSegments(array &$pathSegments, $pathSegmentLookup)
    {
        while (count($pathSegments) > strlen($pathSegmentLookup)) {
            ArrayUtils::removeByIndex($pathSegments, 0);
        }
    }

    /**
     * @param string[] $pathSegments
     * @param UrlReplacement[] $replacements
     * @return string[]
     */
    private function applyReplacementToPathSegment(array $pathSegments, array $replacements)
    {
        if (empty($pathSegments)) {
            return $pathSegments;
        }
        $replacedPathSegments = array();
        $indexOffset = 1;
        foreach ($pathSegments as $pathSegment) {
            $decodedPathSegment = new StringBuilder(urldecode($pathSegment));
            foreach ($replacements as $replacement) {
                $replacement->apply($decodedPathSegment, $indexOffset);
            }
            array_push($replacedPathSegments, $decodedPathSegment->__toString());
            $indexOffset += $decodedPathSegment->length() + 1;
        }
        return $replacedPathSegments;
    }

    /**
     * @param string[] $pathSegments
     * @param Query $query
     * @param string $referenceBlock
     */
    private function addRefinement(array &$pathSegments, Query $query, $referenceBlock)
    {
        $token = $referenceBlock[strlen($referenceBlock) - count($pathSegments)];
        if ($token == self::$searchNavigation->getDisplayName()) {
            $query->setQuery(ArrayUtils::removeByIndex($pathSegments, 0));
        } else if ($this->getFieldName($token) != null) {
            $query->addValueRefinement($this->getFieldName($token), ArrayUtils::removeByIndex($pathSegments, 0));
        } else {
            ArrayUtils::removeByIndex($pathSegments, 0);
        }
    }

    private function getFieldName($token)
    {
        $mapping = $this->tokenToName[$token];
        return $mapping == null ? null : $mapping->getName();
    }

    /**
     * @param string $token
     * @return UrlBeautifier
     */
    public function setSearchMapping($token)
    {
        self::$searchNavigation->setName(Beauty::SEARCH_NAVIGATION_NAME);
        self::$searchNavigation->setDisplayName($token);
        $this->addMapping(self::$searchNavigation);
        return $this;
    }

    /**
     * @param Navigation $mapping
     * @throws RuntimeException
     */
    private function addMapping(Navigation $mapping)
    {
        $name = $mapping->getName();
        $token = $mapping->getDisplayName();
        if (strlen($token) != 1 || empty($token)) {
            throw new RuntimeException("Token length must be one");
        }
        if (preg_match(Beauty::VOWELS, $token)) {
            throw new RuntimeException("Vowels are not allowed to avoid dictionary words appearing");
        }
        if (array_key_exists($token, $this->tokenToName)) {
            throw new RuntimeException(
                "This token: " . $token . " is already mapped to: " . $this->tokenToName[$token]->getName());
        }
        $this->tokenToName[$token] = $mapping;
        $this->nameToToken[$name] = $mapping;
        array_push($this->remainingMappings, $mapping);
    }

    /**
     * @param string $token
     * @param string $name
     * @return UrlBeautifier
     * @throws RuntimeException
     */
    public function addRefinementMapping($token, $name)
    {
        $mapping = new Navigation();
        $this->setValues($token, $name, $mapping);
        $this->addMapping($mapping);
        return $this;
    }

    /**
     * @param string $token
     * @param string $name
     * @param Navigation $mapping
     */
    private function setValues($token, $name, Navigation $mapping)
    {
        $mapping->setDisplayName($token);
        $mapping->setName($name);
    }

    public function clearSavedFields()
    {
        $this->append = null;
        $this->tokenToName = array();
        $this->nameToToken = array();
        $this->remainingMappings = array();
    }

    /**
     * @return string
     */
    public function getAppend()
    {
        return $this->append;
    }

    /**
     * @param string $append
     * @return UrlBeautifier
     */
    public function setAppend($append)
    {
        $this->append = $append;
        return $this;
    }

    /**
     * @return string
     */
    public function getRefinementsQueryParameterName()
    {
        return $this->refinementsQueryParamName;
    }

    /**
     * @param string $refinementsQueryParamName
     */
    public function setRefinementsQueryParamName($refinementsQueryParamName)
    {
        $this->refinementsQueryParamName = $refinementsQueryParamName;
    }

    /**
     * @param string $target
     * @param string $replacement
     * @param string $refinementName
     * @return UrlBeautifier
     */
    public function addReplacementRule($target, $replacement, $refinementName = null)
    {
        if ($target != $replacement) {
            array_push($this->replacementRules, new UrlReplacementRule($target, $replacement, $refinementName));
        }
        return $this;
    }
}

UrlBeautifier::init();
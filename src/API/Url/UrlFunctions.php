<?php

namespace GroupByInc\API\Url;

use GroupByInc\API\Model\Refinement;
use GroupByInc\API\Model\Query;
use GroupByInc\API\Util\ArrayUtils;
use RuntimeException;

class UrlFunctions
{

    /**
     * @param string       $identifier The unique url beautifier identifier.
     * @param string       $searchString
     * @param Refinement[] $refinements
     * @param Refinement   $refinement
     * @return string
     * @throws RuntimeException
     */
    public static function toUrlAdd($identifier, $searchString, array $refinements, Refinement $refinement)
    {
        $beautifier = self::getBeautifier($identifier);
        $query = self::addRefinements($refinements, $refinement);
        return $beautifier->toUrl($searchString, $query->getRefinementString());
    }

    /**
     * @param Query        $query
     * @param string       $identifier
     * @param string       $searchString
     * @param Refinement[] $refinements
     * @param Refinement   $refinement
     * @return string
     * @throws RuntimeException
     */
    public static function toUrlRemove(Query $query, $identifier, $searchString, array $refinements, Refinement $refinement)
    {
        $beautifier = self::getBeautifier($identifier);
        $trimmedRefinements = self::removeRefinements($query, $refinements, $refinement);
        return $beautifier->toUrl($searchString, $trimmedRefinements->getRefinementString());
    }

    /**
     * @param Query        $query
     * @param Refinement[] $refinements
     * @param Refinement   $refinement
     * @return Query
     * @throws RuntimeException
     */
    private static function removeRefinements(Query $query, array $refinements, Refinement $refinement)
    {
        $query->setRefinements(array_merge($query->getRefinements(), $refinements));
        $stringRefinements = $query->getRefinementString();
        $query->addRefinementsByString($stringRefinements);
        $refinements = $query->getRefinements();
        if ($refinements == null) {
            throw new RuntimeException("No existing refinements so cannot remove a refinement");
        }
        if ($refinement != null) {
            foreach ($refinements as $r) {
                if ($r->toTildeString() == $refinement->toTildeString()) {
                    ArrayUtils::remove($refinements, $r);
                }
            }
        }
        $query = new Query();
        $query->setRefinements(array_merge($query->getRefinements(), $refinements));
        return $query;
    }

    /**
     * @param Refinement[] $refinements
     * @param Refinement   $refinement
     * @return Query
     */
    private static function addRefinements(array $refinements, Refinement $refinement)
    {
        $query = new Query();
        if ($refinements != null) {
            $arr = array_merge($query->getRefinements(), $refinements);
            $query->setRefinements($arr);
        }
        if ($refinement != null) {
            array_push($query->getRefinements(), $refinement);
        }
        return $query;
    }

    /**
     * @param string $identifier
     * @return UrlBeautifier
     * @throws RuntimeException
     */
    private static function getBeautifier($identifier)
    {
        $beautifiers = UrlBeautifier::getUrlBeautifiers();
        $beautifier = $beautifiers[$identifier];
        if ($beautifier == null) {
            throw new RuntimeException("Error: could not find UrlBeautifier named: " . $identifier . ". Please call
            UrlBeautifier::createUrlBeautifier(string) to instantiate");
        }
        return $beautifier;
    }
}
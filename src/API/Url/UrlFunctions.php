<?php

namespace GroupByInc\API\Url;

use GroupByInc\API\Model\Navigation;
use GroupByInc\API\Model\SelectedRefinement;
use GroupByInc\API\Query;
use GroupByInc\API\Util\ArrayUtils;
use RuntimeException;

class UrlFunctions
{

    /**
     * @param string       $identifier     The unique url beautifier identifier.
     * @param string       $searchString
     * @param Navigation[] $navigations    List of currently selected navigations. It will be updated to include the
     *                                     specified refinement
     * @param string       $navigationName Name of the navigation to add the refinement to.
     * @param SelectedRefinement   $refinement     Refinement to add.
     * @return string
     * @throws RuntimeException
     */
    public static function toUrlAdd($identifier, $searchString, array &$navigations, $navigationName, SelectedRefinement $refinement)
    {
        $beautifier = self::getBeautifier($identifier);
        $query = self::addRefinements($navigations, $navigationName, $refinement);
        $navigations = $query->getNavigations();
        return $beautifier->toUrl($searchString, $query->getRefinementString());
    }

    /**
     * @param string       $identifier
     * @param string       $searchString
     * @param Navigation[] $navigations    List of currently selected navigations. It will be updated to no longer
     *                                     include the specified refinement.
     * @param string       $navigationName Name of the navigation to remove the refinement from.
     * @param SelectedRefinement   $refinement     Refinement to remove.
     * @return string Updated URL.
     * @throws RuntimeException
     */
    public static function toUrlRemove($identifier, $searchString, array &$navigations, $navigationName, SelectedRefinement $refinement)
    {
        $beautifier = self::getBeautifier($identifier);
        $query = self::removeRefinements($navigations, $navigationName, $refinement);
        $navigations = $query->getNavigations();
        return $beautifier->toUrl($searchString, $query->getRefinementString());
    }

    /**
     * @param Navigation[] $navigations
     * @param string       $navigationName
     * @param SelectedRefinement   $refinement
     * @return Query
     * @throws RuntimeException
     */
    private static function removeRefinements(array $navigations, $navigationName, SelectedRefinement $refinement)
    {
        $query = new Query();
        $newNavigations = &$query->getNavigations();

        foreach ($navigations as $n) {
            $newNavigations[$n->getName()] = $n;
        }
        $stringRefinements = $query->getRefinementString();
        $query = new Query();
        $query->addRefinementsByString($stringRefinements);
        $newNavigations = &$query->getNavigations();
        if ($newNavigations == null) {
            throw new RuntimeException("No existing refinements so cannot remove a refinement");
        }

        if ($refinement != null) {
            foreach ($newNavigations as $n) {
                if ($n->getName() == $navigationName) {
                    $refinements = &$n->getRefinements();
                    foreach ($refinements as $r) {
                        if ($r->toTildeString() == $refinement->toTildeString()) {
                            ArrayUtils::remove($refinements, $r);
                        }
                    }
                    if (empty($refinements)) {
                        ArrayUtils::remove($newNavigations, $n);
                    }
                }
            }
        }
        return $query;
    }

    /**
     * @param Navigation[] $navigations
     * @param string       $navigationName
     * @param SelectedRefinement   $refinement
     * @return Query
     */
    private static function addRefinements(array $navigations, $navigationName, SelectedRefinement $refinement)
    {
        $query = new Query();
        $newNavigations = &$query->getNavigations();
        if ($navigations != null) {
            foreach ($navigations as $n) {
                $newNavigations[$n->getName()] = $n;
            }
        }
        if (!array_key_exists($navigationName, $newNavigations)) {
            $navigation = new Navigation();
            $navigation->setName($navigationName);
            $newNavigations[$navigationName] = $navigation;
        }
        if ($refinement != null) {
            array_push($query->getNavigations()[$navigationName]->getRefinements(), $refinement);
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
        if (!array_key_exists($identifier, $beautifiers)) {
            throw new RuntimeException("Error: could not find UrlBeautifier named: " . $identifier .
                ". Please call UrlBeautifier::createUrlBeautifier(string) to instantiate");
        }
        return $beautifiers[$identifier];
    }
}
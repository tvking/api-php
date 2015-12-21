<?php

class Json
{
    public static $RICH_CONTENT_ZONE = '{"richContent":"crestfallen","_id":"f90j1e1rf","name":"appalled","type":"Rich_Content"}';
    public static $CONTENT_ZONE = '{"content":"mushy","_id":"23425n89hr","name":"porcelain","type":"Content"}';
    public static $BANNER_ZONE = '{"bannerUrl":"man","_id":"asf0j2380jf","name":"vitruvian","type":"Banner"}';
    public static $CLUSTER_RECORD = '{"title":"fubar","url":"example.com","snippet":"itty bit"}';
    public static $REFINEMENT_MATCH_VALUE = '{"value":"adverb","count":43}';
    public static $METADATA = '{"key":"orange","value":"apple"}';
    public static $REFINEMENT_VALUE = '{"_id":"fadfs89y10j","count":987,"type":"Value","value":"malaise","exclude":false}';
    public static $REFINEMENT_RANGE = '{"high":"delicious","low":"atrocious","_id":"342h9582hh4","count":14,"type":"Range","exclude":true}';
    public static $PAGE_INFO = '{"recordStart":20,"recordEnd":50}';
    public static $RESTRICT_NAVIGATION = '{"name":"categories","count":2}';
    public static $CUSTOM_URL_PARAM = '{"key":"guava","value":"mango"}';
    public static $SORT = '{"field":"price","order":"Descending"}';
    public static $PARTIAL_MATCH_RULE = '{"terms":2,"termsGreaterThan":45,"mustMatch":4,"percentage":true}';
    public static $REFINEMENT_MATCH;
    public static $RECORD;
    public static $RECORD_ZONE;
    public static $TEMPLATE;
    public static $CLUSTER;
    public static $NAVIGATION;
    public static $MATCH_STRATEGY;
    public static $REQUEST;
    public static $REFINEMENTS_REQUEST;
    public static $RESULTS;
    public static $REFINEMENT_RESULTS;

    public static function init()
    {
        self::$REFINEMENT_MATCH = '{"name":"grapheme","values":[' . self::$REFINEMENT_MATCH_VALUE . ']}';

        self::$RECORD = '{"_id":"fw90314jh289t","_u":"exemplar.com","_snippet":"Curator","_t":"Periwinkle",' .
            '"allMeta":{"look":"at","all":"my","keys":["we","are","the","values"]},"refinementMatches":[' .
            self::$REFINEMENT_MATCH . ']}';

        self::$RECORD_ZONE = '{"records":[' . self::$RECORD . '],"query":"searchTerms","_id":"1240jfw9s8",' .
            '"name":"gorbachev","type":"Record"}';

        self::$TEMPLATE = '{"_id":"fad87g114","name":"bulbous","ruleName":"carmageddon",' .
            '"zones":{"content_zone":' . self::$CONTENT_ZONE . ',"record_zone":' . self::$RECORD_ZONE . '}}';

        self::$CLUSTER = '{"term":"some","records":[' . self::$CLUSTER_RECORD . ']}';

        self::$NAVIGATION = '{"_id":"081h29n81f","name":"green","displayName":"GReeN",' .
            '"range":true,"or":false,"type":"Range_Date","sort":' . self::$SORT . ',"refinements":[' .
            self::$REFINEMENT_RANGE . ',' . self::$REFINEMENT_VALUE .
            '],"metadata":[' . self::$METADATA . '],"moreRefinements":true}';

        self::$MATCH_STRATEGY = '{"rules":[' . self::$PARTIAL_MATCH_RULE . ']}';

        self::$REQUEST = '{"clientKey":"adf7h8er7h2r","collection":"ducks",' .
            '"area":"surface","skip":12,"pageSize":30,"biasingProfile":"ballooning","language":"en",' .
            '"pruneRefinements":true,"returnBinary":false,"query":"cantaloupe",' .
            '"sort":[' . self::$SORT . '],"fields":["pineapple","grape","clementine"],' .
            '"orFields":["pumpernickel","rye"],"includedNavigations":["height"], "excludedNavigations":["rating"],' .
            '"refinements":[' . self::$REFINEMENT_RANGE . ',' . self::$REFINEMENT_VALUE .
            '],' . '"customUrlParams":[' . self::$CUSTOM_URL_PARAM .
            '],' . '"restrictNavigation":' . self::$RESTRICT_NAVIGATION . ',"refinementQuery":"cranberry",' .
            '"wildcardSearchEnabled":true,"matchStrategy":' . self::$MATCH_STRATEGY . '}';

        self::$REFINEMENTS_REQUEST = '{"originalQuery":' . self::$REQUEST . ',"navigationName":"height"}';

        self::$RESULTS = '{"availableNavigation":[' . self::$NAVIGATION . '],' .
            '"selectedNavigation":[' . self::$NAVIGATION . '],' .
            '"clusters":[' . self::$CLUSTER . '],"records":[' . self::$RECORD . '],' .
            '"didYouMean":["square","skewer"],"relatedQueries":["squawk","ask"],' .
            '"siteParams":[' . self::$METADATA . '],"rewrites":["Synonym","Antonym","Homonym"],' .
            '"pageInfo":' . self::$PAGE_INFO . ',"template":' . self::$TEMPLATE . ',' .
            '"redirect":"/to/the/moon.html","errors":"criminey!","query":"skwuare","area":"christmas",' .
            '"totalRecordCount":34,"biasingProfile":"unbiased","originalQuery":"skwuare---","correctedQuery":"square"}';

        self::$REFINEMENT_RESULTS = '{"errors":"Could not load","navigation":' . self::$NAVIGATION . '}';
    }
}

Json::init();

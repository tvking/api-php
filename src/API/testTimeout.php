<?php

/**
 * TEST PLAN
 * 
 * To test read timeouts, create a file at localhost/search that contains:
 *
 * <?php
 * sleep(60);
 *
 * run: php src/API/testTimeout.php
 *
 * expected output:
 *
 * sending request {"clientKey":"blah blah","query":"dvd","sort":[],"fields":[],"orFields":[],"refinements":[],"customUrlParams":[],"skip":0,"pageSize":10,"pruneRefinements":true,"wildcardSearchEnabled":false} to http://localhost//searchOperation timed out after 2001 milliseconds with 0 bytes received
 * Connection failed, retrying
 * sending request {"clientKey":"blah blah","query":"dvd","sort":[],"fields":[],"orFields":[],"refinements":[],"customUrlParams":[],"skip":0,"pageSize":10,"pruneRefinements":true,"wildcardSearchEnabled":false} to http://localhost//searchOperation timed out after 2001 milliseconds with 0 bytes received
 * Connection failed, retrying
 * sending request {"clientKey":"blah blah","query":"dvd","sort":[],"fields":[],"orFields":[],"refinements":[],"customUrlParams":[],"skip":0,"pageSize":10,"pruneRefinements":true,"wildcardSearchEnabled":false} to http://localhost//searchOperation timed out after 2001 milliseconds with 0 bytes received
 * Connection failed, retrying
 * PHP Fatal error:  Uncaught exception 'Httpful\Exception\ConnectionErrorException' with message 'Unable to connect to "http://localhost//search?retry=2": 28 Operation timed out after 2001 milliseconds with 0 bytes received' in ~/api-php/vendor/nategood/httpful/src/Httpful/Request.php:1059
 * Stack trace:
 *
 * Test a connection timeout drop all tcp traffic on port 80
 * sudo iptables -A INPUT -p tcp --destination-port 80 -j DROP
 * 
 * run: php src/API/testTimeout.php
 *
 * expected output:
 * sending request {"clientKey":"blah blah","query":"dvd","sort":[],"fields":[],"orFields":[],"refinements":[],"customUrlParams":[],"skip":0,"pageSize":10,"pruneRefinements":true,"wildcardSearchEnabled":false} to http://localhost//searchConnection timed out after 1001 milliseconds
 * Connection failed, retrying
 * sending request {"clientKey":"blah blah","query":"dvd","sort":[],"fields":[],"orFields":[],"refinements":[],"customUrlParams":[],"skip":0,"pageSize":10,"pruneRefinements":true,"wildcardSearchEnabled":false} to http://localhost//searchConnection timed out after 1001 milliseconds
 * Connection failed, retrying
 * sending request {"clientKey":"blah blah","query":"dvd","sort":[],"fields":[],"orFields":[],"refinements":[],"customUrlParams":[],"skip":0,"pageSize":10,"pruneRefinements":true,"wildcardSearchEnabled":false} to http://localhost//searchConnection timed out after 1000 milliseconds
 * Connection failed, retrying
 * PHP Fatal error:  Uncaught exception 'Httpful\Exception\ConnectionErrorException' with message 'Unable to connect to "http://localhost//search?retry=2": 28 Connection timed out after 1000 milliseconds' in ~/api-php/vendor/nategood/httpful/src/Httpful/Request.php:1059
 * Stack trace:
 *
 * revert the iptables change 
 * sudo iptables -D INPUT -p tcp --destination-port 80 -j DROP
 */

namespace GroupByInc\API;

// Including global autoloader
require_once dirname(__FILE__) . '/../../vendor/autoload.php';


class MyAutoloader
{
    public static function load($className)
    {
        require '../' . __NAMESPACE__ . $className . '.inc.php';
    }
}

spl_autoload_register(__NAMESPACE__ . "\\MyAutoloader::load");

class DevBridge extends AbstractBridge
{
    function __construct()
    {
        parent::__construct('blah blah', 'http://localhost/');
    }
}

$bridge = new DevBridge;
$bridge->setConnectionTimeout(1);
$bridge->setTimeout(2);
$query = new Query;
$query->setQuery('dvd');
$results = $bridge->search($query);

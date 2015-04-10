<?php

use GroupByInc\API\CloudBridge;
use GroupByInc\API\Model\RefinementsResult;
use GroupByInc\API\Model\Results;
use GroupByInc\API\Query;
use Httpful\Request;
use Httpful\Response;

class CloudBridgeTest extends PHPUnit_Framework_TestCase
{
    const CLIENT_KEY = 'randomkey';
    const DOMAIN = 'testdomain';
    const SEARCH_URL = 'https://testdomain.groupbycloud.com:443/api/v1/search';
    const REFINEMENT_SEARCH_URL = 'https://testdomain.groupbycloud.com:443/api/v1/search/refinements';
    const HEADERS = "Status 200\r\nContent-Type:application/json\n";
    // Must match expected bridge json
    const TEST_QUERY = '{"clientKey":"randomkey","fields":[],"orFields":[],"refinements":[],"customUrlParams":[],"skip":0,"pageSize":10}';
    const TEST_RESPONSE = '{"query":"foobar","pageInfo":{"recordStart":14,"recordEnd":23}}';

    public function testErroneousStatusCode()
    {
        $bridge = Phake::partialMock('GroupByInc\API\CloudBridge', self::CLIENT_KEY, self::DOMAIN);
        Phake::when($bridge)->execute(self::SEARCH_URL, self::TEST_QUERY)
            ->thenReturn(new Response('{"foo":"bar"}', 'Status 400', Request::post('')));

        $query = new Query();
        try {
            /** @var CloudBridge $bridge */
            $bridge->search($query);
            $this->fail("Should have thrown exception here");
        } catch (RuntimeException $e) {
            if (strpos($e->getMessage(), '404 Not Found') !== false) {
                $this->fail("Expected status code 400, found 404");
            }
            // Should throw exception
        }
    }

    public function testErrorOnReturnBinary()
    {
        $bridge = Phake::partialMock('GroupByInc\API\CloudBridge', self::CLIENT_KEY, self::DOMAIN);
        Phake::when($bridge)->execute(self::SEARCH_URL, self::TEST_QUERY)
            ->thenReturn(new Response('{"foo":"bar"}', "Status 200\r\nContent-Type:application/bson\n", Request::post('')));

        $query = new Query();
        try {
            /** @var CloudBridge $bridge */
            $bridge->search($query);
            $this->fail("Should have thrown exception here");
        } catch (RuntimeException $e) {
            // Should throw exception
        }
    }

    public function testSearch()
    {
        $bridge = Phake::partialMock('GroupByInc\API\CloudBridge', self::CLIENT_KEY, self::DOMAIN);
        Phake::when($bridge)->execute(self::SEARCH_URL, self::TEST_QUERY)
            ->thenReturn(new Response(self::TEST_RESPONSE, self::HEADERS, Request::post('')));

        $query = new Query();
        /** @var CloudBridge $bridge */
        /** @var Results $results */
        $results = $bridge->search($query);
        $this->assertEquals('foobar', $results->getQuery());
    }

    public function testSearchCompressedResponse()
    {
        $bridge = Phake::partialMock('GroupByInc\API\CloudBridge', self::CLIENT_KEY, self::DOMAIN);
        Phake::when($bridge)->execute(self::SEARCH_URL, self::TEST_QUERY)
            ->thenReturn(new Response(self::TEST_RESPONSE, self::HEADERS . "Content-Encoding:gzip\n", Request::post('')));

        $query = new Query();
        /** @var CloudBridge $bridge */
        /** @var Results $results */
        $results = $bridge->search($query);
        $this->assertEquals('foobar', $results->getQuery());
    }

    public function testSearchRefinements()
    {
        $refinementsQuery = '{"originalQuery":' . self::TEST_QUERY . ',"navigationName":"height"}';
        $bridge = Phake::partialMock('GroupByInc\API\CloudBridge', self::CLIENT_KEY, self::DOMAIN);
        Phake::when($bridge)->execute(self::REFINEMENT_SEARCH_URL, $refinementsQuery)
            ->thenReturn(new Response('{"navigation":{"name":"foobar"}}', self::HEADERS, Request::post('')));

        $query = new Query();
        /** @var CloudBridge $bridge */
        /** @var RefinementsResult $results */
        $results = $bridge->refinements($query, "height");
        $this->assertEquals('foobar', $results->getNavigation()->getName());
    }

//    public function testClusterSearch()
//    {
//        $http = Mockery::mock('API\Util\AbstractRequest');
//        $http->shouldReceive('setOption');
//        $http->shouldReceive('getHttpStatusCode')->andReturn(200);
//        $http->shouldReceive('execute')->andReturn(true);
//        $http->shouldReceive('getHttpContentType')->andReturn('application/json');
//        //create fake bridge with empty query, assign the mock http session, try to search, should not get exception
//        $myBridge = new CloudBridge('randomkey', 'somewhere');
//        $myQuery = new Query();
//        /** @noinspection PhpParamsInspection */
//        $myBridge->setSessionCluster($http);
//        $myBridge->searchCluster($myQuery);
//    }
}

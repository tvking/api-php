<?php

use GroupByInc\API\Bridge;
use GroupByInc\API\Query;

class BridgeTest extends PHPUnit_Framework_TestCase
{

    public function testGetThrowsWhenStatusNot200()
    {
        $http = Mockery::mock('API\Util\AbstractRequest')->shouldDeferMissing();
        $http->shouldReceive('getHttpStatusCode')->andReturn("not 200");
        $http->shouldReceive('execute')->andReturn(true);
        $this->setExpectedException('Exception');
        //create fake bridge with empty query, assign the mock http session, try to search, should get exception
        $myBridge = new Bridge('randomkey', 'somewhere', 9050);
        $myQuery = new Query();
        /** @noinspection PhpParamsInspection */
        $myBridge->setSession($http);
        $myBridge->search($myQuery);
    }

    public function testGetThrowsWhenExecReturnsFalse()
    {
        $http = Mockery::mock('API\Util\AbstractRequest');
        $http->shouldReceive('execute')->andReturn(false);
        $this->setExpectedException('Exception');
        //create fake bridge with empty query, assign the mock http session, try to search, should get exception
        $myBridge = new Bridge('randomkey', 'somewhere', 9050);
        $myQuery = new Query();
        /** @noinspection PhpParamsInspection */
        $myBridge->setSession($http);
        $myBridge->search($myQuery);
    }

    public function testGetThrowsWhenReturnBinary()
    {
        $http = Mockery::mock('API\Util\AbstractRequest');
        $http->shouldReceive('getHttpContentType')->andReturn('not json');
        //we simulate a good query that returns 200
        $http->shouldReceive('getHttpStatusCode')->andReturn(200);
        $http->shouldReceive('execute')->andReturn(true);
        $this->setExpectedException('Exception');
        //create fake bridge with empty query, assign the mock http session, try to search, should get exception
        $myBridge = new Bridge('randomkey', 'somewhere', 9050);
        $myQuery = new Query();
        /** @noinspection PhpParamsInspection */
        $myBridge->setSession($http);
        $myBridge->search($myQuery);
    }

    public function testGetNullJsonWhenInvalidJson()
    {
        $http = Mockery::mock('API\Util\AbstractRequest');
        $http->shouldReceive('setOption');
        $http->shouldReceive('getHttpContentType')->andReturn('application/json');
        //we simulate a good query that returns 200
        $http->shouldReceive('getHttpStatusCode')->andReturn(200);
        $http->shouldReceive('execute')->andReturn('this is not JSON!');
        //create fake bridge with empty query, assign the mock http session, try to search, should get exception
        $myBridge = new Bridge('randomkey', 'somewhere', 9050);
        $myQuery = new Query();
        /** @noinspection PhpParamsInspection */
        $myBridge->setSession($http);
        $queryResult = $myBridge->search($myQuery);
        $this->assertNull($queryResult);
    }

//    public function testClusterSearch()
//    {
//        $http = Mockery::mock('API\Util\AbstractRequest');
//        $http->shouldReceive('setOption');
//        $http->shouldReceive('getHttpStatusCode')->andReturn(200);
//        $http->shouldReceive('execute')->andReturn(true);
//        $http->shouldReceive('getHttpContentType')->andReturn('application/json');
//        //create fake bridge with empty query, assign the mock http session, try to search, should not get exception
//        $myBridge = new Bridge('randomkey', 'somewhere', 9050);
//        $myQuery = new Query();
//        /** @noinspection PhpParamsInspection */
//        $myBridge->setSessionCluster($http);
//        $myBridge->searchCluster($myQuery);
//    }
}

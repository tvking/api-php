<?php

use GroupByInc\API\Bridge;
use GroupByInc\API\CloudBridge;
use GroupByInc\API\Query;

class CloudBridgeTest extends PHPUnit_Framework_TestCase
{

    public function testGetThrowsWhenStatusNot200()
    {
        $http = Mockery::mock('API\Util\AbstractRequest')->shouldDeferMissing();
        $http->shouldReceive('getHttpStatusCode')->andReturn("not 200");
        $http->shouldReceive('execute')->andReturn(true);
        $this->setExpectedException('Exception');
        //create fake bridge with empty query, assign the mock http session, try to search, should get exception
        $myBridge = new CloudBridge('randomkey', 'somewhere');
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
        $myBridge = new CloudBridge('randomkey', 'somewhere');
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
        $myBridge = new CloudBridge('randomkey', 'somewhere');
        $myQuery = new Query();
        /** @noinspection PhpParamsInspection */
        $myBridge->setSession($http);
        $myBridge->search($myQuery);
    }

    public function testGetNullJsonWhenInvalidJson()
    {
        $headers = 'content-type:application/json\n';
        $content = 'this is not JSON!';
        $http = Mockery::mock('API\Util\AbstractRequest');
        $http->shouldReceive('setOption');
        $http->shouldReceive('getHttpContentType')->andReturn('application/json');
        //we simulate a good query that returns 200
        $http->shouldReceive('getHttpStatusCode')->andReturn(200);
        $http->shouldReceive('execute')->andReturn($headers . $content);
        $http->shouldReceive('getInfo')->withArgs(array(CURLINFO_HEADER_SIZE))->andReturn(31);
        //create fake bridge with empty query, assign the mock http session, try to search, should get exception
        $myBridge = new CloudBridge('randomkey', 'somewhere');
        $myQuery = new Query();
        /** @noinspection PhpParamsInspection */
        $myBridge->setSession($http);
        $queryResult = $myBridge->search($myQuery);
        $this->assertNull($queryResult);
    }

    public function testSearchUncompressedResponse()
    {
        $headers = 'content-type:application/json\n';
        $content = '{"query":"queryString"}';
        $http = Mockery::mock('API\Util\AbstractRequest');
        $http->shouldReceive('setOption');
        $http->shouldReceive('getHttpContentType')->andReturn('application/json');
        //we simulate a good query that returns 200
        $http->shouldReceive('getHttpStatusCode')->andReturn(200);
        $http->shouldReceive('execute')->andReturn($headers . $content);
        $http->shouldReceive('getInfo')->withArgs(array(CURLINFO_HEADER_SIZE))->andReturn(31);

        //create fake bridge with empty query, assign the mock http session, try to search, should not get exception
        $myBridge = new CloudBridge('randomkey', 'somewhere');
        $myQuery = new Query();

        $myBridge->setSession($http);
        $queryResult = $myBridge->search($myQuery);
        $this->assertEquals("queryString", $queryResult->getQuery());
    }

    public function testSearchCompressedResponse()
    {
        $headers = 'content-encoding:gzip\n';
        $content = '{"query":"queryString"}';
        $contentCompressed = gzencode($content);
        $http = Mockery::mock('API\Util\AbstractRequest');
        $http->shouldReceive('setOption');
        $http->shouldReceive('getHttpContentType')->andReturn('application/json');
        //we simulate a good query that returns 200
        $http->shouldReceive('getHttpStatusCode')->andReturn(200);
        $http->shouldReceive('execute')->andReturn($headers . $contentCompressed);
        $http->shouldReceive('getInfo')->withArgs(array(CURLINFO_HEADER_SIZE))->andReturn(23);

        //create fake bridge with empty query, assign the mock http session, try to search, should not get exception
        $myBridge = new CloudBridge('randomkey', 'somewhere');
        $myQuery = new Query();
        $myQuery->setCompressResponse(true);
        $myBridge->setSession($http);
        $queryResult = $myBridge->search($myQuery);
        $this->assertEquals("queryString", $queryResult->getQuery());
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

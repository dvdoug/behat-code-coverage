<?php
/**
 * Code Coverage Driver 
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Driver\RemoteXdebug;

/**
 * Remote driver test
 *
 * @group Unit
 */
class RemoteXdebugTest extends TestCase
{
    private $config;

    private $client;

    private $response;

    protected function setUp()
    {
        parent::setUp();

        $this->config = array(
            'base_url' => 'http://localhost',
            'auth'     => array(
                              'user'     => 'user name',
                              'password' => 'password',
                          ),
            'create'   => array(
                              'method' => 'POST',
                              'path'   => '/',
                          ),
            'read'     => array(
                              'method' => 'GET',
                              'path'   => '/',
                          ),
            'delete'   => array(
                              'method' => 'DELETE',
                              'path'   => '/',
                          ),
        );

        $this->response = $this->getMockBuilder('Guzzle\Http\Message\Response')
                               ->disableOriginalConstructor()
                               ->getMock();

        $request = $this->getMockBuilder('Guzzle\Http\Message\Request')
                        ->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->any())
                ->method('send')
                ->will($this->returnValue($this->response));

        $this->client = $this->createMock('Guzzle\Http\Client');
        $this->client->expects($this->any())
                     ->method('post')
                     ->will($this->returnValue($request));
        $this->client->expects($this->any())
                     ->method('put')
                     ->will($this->returnValue($request));
        $this->client->expects($this->any())
                     ->method('get')
                     ->will($this->returnValue($request));
        $this->client->expects($this->any())
                     ->method('delete')
                     ->will($this->returnValue($request));
    }

    public function testInvalidMethodException()
    {
        try {
            $this->config['create']['method'] = 'TRACE';

            $driver = new RemoteXdebug($this->config, $this->client);
            $driver->start();

            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), 'method must be GET, POST, PUT, or DELETE') !== false);
        }
    }

    public function testStart()
    {
        $this->response->expects($this->once())
                       ->method('getStatusCode')
                       ->will($this->returnValue(200));

        $driver = new RemoteXdebug($this->config, $this->client);
        $driver->start();
    }

    public function testStartException()
    {
        try {
            $driver = new RemoteXdebug($this->config, $this->client);
            $driver->start();

            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), 'remote driver start failed: ') === 0);
        }
    }

    public function testStop()
    {
        $this->response->expects($this->once())
                       ->method('getStatusCode')
                       ->will($this->returnValue(200));

        $driver   = new RemoteXdebug($this->config, $this->client);
        $coverage = $driver->stop();
    }

    public function testStopException()
    {
        try {
            $driver = new RemoteXdebug($this->config, $this->client);

            $coverage = $driver->stop();

            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), 'remote driver fetch failed: ') === 0);
        }
    }
}

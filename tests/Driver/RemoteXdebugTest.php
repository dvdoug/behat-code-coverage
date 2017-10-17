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
            'base_uri' => 'http://localhost',
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

        $this->response = $this->getMockBuilder('GuzzleHttp\Message\Response')
                               ->disableOriginalConstructor()
                               ->getMock();

        $request = $this->getMockBuilder('GuzzleHttp\Message\Request')
                        ->disableOriginalConstructor()
                        ->getMock();

        $response = $this->getMockBuilder('GuzzleHttp\Message\Response')
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->client = $this->createMock('GuzzleHttp\Client');
        $this->client->expects($this->any())
                     ->method('post')
                     ->will($this->returnValue($response));
        $this->client->expects($this->any())
                     ->method('put')
                     ->will($this->returnValue($response));
        $this->client->expects($this->any())
                     ->method('get')
                     ->will($this->returnValue($response));
        $this->client->expects($this->any())
                     ->method('delete')
                     ->will($this->returnValue($response));
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
        $driver = new RemoteXdebug($this->config, $this->client);

        try {
            $driver->start();
        } catch (\Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), 'remote driver start failed: ') === 0);
        }

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
        $driver   = new RemoteXdebug($this->config, $this->client);

        try {
             $coverage = $driver->stop();
        } catch (\Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), 'remote driver fetch failed: ') === 0);
        }
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

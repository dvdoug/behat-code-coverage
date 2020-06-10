<?php

declare(strict_types=1);
/**
 * Code Coverage Driver.
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace DVDoug\Behat\CodeCoverage;

use DVDoug\Behat\CodeCoverage\Driver\RemoteXdebug;
use PHPUnit\Framework\TestCase;

/**
 * Remote driver test.
 *
 * @group Unit
 */
class RemoteXdebugTest extends TestCase
{
    private $config;

    private $client;

    private $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'base_uri' => 'http://localhost',
            'auth' => [
                              'user' => 'user name',
                              'password' => 'password',
                          ],
            'create' => [
                              'method' => 'POST',
                              'path' => '/',
                          ],
            'read' => [
                              'method' => 'GET',
                              'path' => '/',
                          ],
            'delete' => [
                              'method' => 'DELETE',
                              'path' => '/',
                          ],
        ];

        $this->response = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
                               ->disableOriginalConstructor()
                               ->getMock();

        $request = $this->getMockBuilder('GuzzleHttp\Psr7\Request')
                        ->disableOriginalConstructor()
                        ->getMock();

        $response = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
                        ->disableOriginalConstructor()
                        ->getMock();

        $response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn('302');

        $this->client = $this->createMock('GuzzleHttp\Client');
        $this->client->expects($this->any())
            ->method('request')
                     ->willReturn($response);
    }

    public function testInvalidMethodException(): void
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

    public function testStart(): void
    {
        $driver = new RemoteXdebug($this->config, $this->client);

        try {
            $driver->start();
        } catch (\Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), 'remote driver start failed: ') === 0);
        }
    }

    public function testStartException(): void
    {
        try {
            $driver = new RemoteXdebug($this->config, $this->client);
            $driver->start();

            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), 'remote driver start failed: ') === 0);
        }
    }

    public function testStop(): void
    {
        $driver = new RemoteXdebug($this->config, $this->client);

        try {
            $coverage = $driver->stop();
        } catch (\Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), 'remote driver fetch failed: ') === 0);
        }
    }

    public function testStopException(): void
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

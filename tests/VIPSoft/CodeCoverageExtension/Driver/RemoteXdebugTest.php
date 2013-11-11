<?php
/**
 * Code Coverage Driver 
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension;

use VIPSoft\TestCase;
use VIPSoft\CodeCoverageExtension\Driver\RemoteXdebug;

/**
 * Remote driver test
 *
 * @group Unit
 */
class RemoteXdebugTest extends TestCase
{
    private $config;

    private $guzzleClient;

    private $response;

    public function __construct()
    {
        if ( ! class_exists('VIPSoft\CodeCoverageExtension\Test\Client')) {
            eval(<<<END_OF_CLIENT
namespace VIPSoft\CodeCoverageExtension\Test {
    class Client
    {
        static public \$proxiedMethods;

        public function __call(\$methodName, \$args)
        {
            if (isset(self::\$proxiedMethods[\$methodName])) {
                return call_user_func_array(self::\$proxiedMethods[\$methodName], \$args);
            }
        }
    }
}
END_OF_CLIENT
            );
        }

        $this->guzzleClient = '\VIPSoft\CodeCoverageExtension\Test\Client';
    }

    protected function setUp()
    {
        $this->config = array(
            'baseUrl' => 'http://localhost',
            'auth'    => array(
                             'user'     => 'user name',
                             'password' => 'password',
                         ),
            'create'  => array(
                             'method' => 'POST',
                             'path'   => '/',
                         ),
            'read'    => array(
                             'method' => 'GET',
                             'path'   => '/',
                         ),
            'delete'  => array(
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

        $mockClientClass = $this->guzzleClient;
        $mockClientClass::$proxiedMethods = array(
            'post' => function ($url) use ($request) {
                return $request;
            },
            'put' => function ($url) use ($request) {
                return $request;
            },
            'get' => function ($url) use ($request) {
                return $request;
            },
            'delete' => function ($url) use ($request) {
                return $request;
            },
        );
    }

    public function testInvalidMethodException()
    {
        try {
            $this->config['create']['method'] = 'TRACE';

            $driver = new RemoteXdebug($this->config, $this->guzzleClient);
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

        $driver = new RemoteXdebug($this->config, $this->guzzleClient);
        $driver->start();
    }

    public function testStartException()
    {
        try {
            $driver = new RemoteXdebug($this->config, $this->guzzleClient);
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

        $driver   = new RemoteXdebug($this->config, $this->guzzleClient);
        $coverage = $driver->stop();
    }

    public function testStopException()
    {
        try {
            $driver = new RemoteXdebug($this->config, $this->guzzleClient);

            $coverage = $driver->stop();

            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), 'remote driver fetch failed: ') === 0);
        }
    }
}

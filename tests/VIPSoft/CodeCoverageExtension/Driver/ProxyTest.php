<?php
/**
 * Code Coverage Driver 
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension;

use VIPSoft\TestCase;
use VIPSoft\CodeCoverageExtension\Driver\Proxy;

/**
 * Proxy driver test
 *
 * @group Unit
 */
class ProxyTest extends TestCase
{
    private $driver;
    private $localDriver;
    private $remoteDriver;

    protected function setUp()
    {
        parent::setUp();

        $this->localDriver = $this->getMock('VIPSoft\CodeCoverageCommon\Driver\Stub');

        $this->remoteDriver = $this->getMockBuilder('VIPSoft\CodeCoverageExtension\Driver\RemoteXdebug')
                                   ->disableOriginalConstructor()
                                   ->getMock();

        $this->driver = new Proxy();
        $this->driver->addDriver($this->localDriver);
        $this->driver->addDriver($this->remoteDriver);
    }

    public function testStart()
    {
        $this->localDriver->expects($this->once())
                          ->method('start');

        $this->remoteDriver->expects($this->once())
                           ->method('start');

        $this->driver->start();
    }

    public function testStop()
    {
        $coverage = array(
                        'SomeClass' => array(
                            1 => 1,
                            2 => -1,
                            3 => -2,
                        )
                    );

        $this->localDriver->expects($this->once())
                          ->method('stop')
                          ->will($this->returnValue($coverage));

        $this->remoteDriver->expects($this->once())
                           ->method('stop')
                          ->will($this->returnValue(null));

        $coverage = $this->driver->stop();

        $this->assertEquals(
            array(
                'SomeClass' => array(
                    1 => 1,
                    2 => -1,
                    3 => -2,
                )
            ),
            $coverage
        );
    }
}

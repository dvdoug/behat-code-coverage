<?php
/**
 * Code Coverage Driver 
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Driver\Proxy;

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

        $this->localDriver = $this->createMock('LeanPHP\Behat\CodeCoverage\Common\Driver\Stub');

        $this->remoteDriver = $this->getMockBuilder('LeanPHP\Behat\CodeCoverage\Driver\RemoteXdebug')
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

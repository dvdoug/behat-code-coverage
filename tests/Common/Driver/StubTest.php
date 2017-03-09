<?php
/**
 * Code Coverage Driver 
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Driver;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Driver\Stub;

/**
 * Stub driver test
 *
 * @group Unit
 */
class StubTest extends TestCase
{
    public function testGetterSetter()
    {
        $mock = $this->getMock('PHP_CodeCoverage_Driver_Xdebug');

        $driver = new Stub();
        $this->assertTrue($driver->getDriver() === null);

        $driver->setDriver($mock);
        $this->assertTrue($driver->getDriver() === $mock);
    }

    public function testStart()
    {
        $mock = $this->getMock('PHP_CodeCoverage_Driver_Xdebug');
        $mock->expects($this->once())
             ->method('start');

        $driver = new Stub();
        $driver->setDriver($mock);
        $driver->start();
    }

    public function testStop()
    {
        $mock = $this->getMock('PHP_CodeCoverage_Driver_Xdebug');
        $mock->expects($this->once())
             ->method('stop');

        $driver = new Stub();
        $driver->setDriver($mock);
        $driver->stop();
    }
}

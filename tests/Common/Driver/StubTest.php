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
    /**
     * @requires OS Linux
     */
    public function testGetterSetterXdebug()
    {
        $mock = $this->getMock('SebastianBergmann\CodeCoverage\Driver\Xdebug');

        $driver = new Stub();
        $this->assertTrue($driver->getDriver() === null);

        $driver->setDriver($mock);
        $this->assertTrue($driver->getDriver() === $mock);
    }

    /**
     * @requires OS Linux
     */
    public function testStartXdebug()
    {
        $mock = $this->getMock('SebastianBergmann\CodeCoverage\Driver\Xdebug');
        $mock->expects($this->once())
             ->method('start');

        $driver = new Stub();
        $driver->setDriver($mock);
        $driver->start();
    }

    /**
     * @requires OS Linux
     */
    public function testStopXdebug()
    {
        $mock = $this->getMock('SebastianBergmann\CodeCoverage\Driver\Xdebug');
        $mock->expects($this->once())
             ->method('stop');

        $driver = new Stub();
        $driver->setDriver($mock);
        $driver->stop();
    }

    /**
     * @requires OS WIN
     */
    public function testGetterSetterPHPDBG()
    {
        $mock = $this->getMock('SebastianBergmann\CodeCoverage\Driver\PHPDBG');

        $driver = new Stub();
        $this->assertTrue($driver->getDriver() === null);

        $driver->setDriver($mock);
        $this->assertTrue($driver->getDriver() === $mock);
    }

    /**
     * @requires OS WIN
     */
    public function testStartPHPDBG()
    {
        $mock = $this->getMock('SebastianBergmann\CodeCoverage\Driver\PHPDBG');
        $mock->expects($this->once())
             ->method('start');

        $driver = new Stub();
        $driver->setDriver($mock);
        $driver->start();
    }

    /**
     * @requires OS WIN
     */
    public function testStopPHPDBG()
    {
        $mock = $this->getMock('SebastianBergmann\CodeCoverage\Driver\PHPDBG');
        $mock->expects($this->once())
             ->method('stop');

        $driver = new Stub();
        $driver->setDriver($mock);
        $driver->stop();
    }

}

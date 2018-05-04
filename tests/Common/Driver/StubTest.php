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
/**
 * TODO - reimplement integration tests'
 *
class StubTest extends TestCase
{
    /**
     * @requires extension xdebug
    public function testGetterSetterXdebug()
    {
        $mock = $this->createMock('SebastianBergmann\CodeCoverage\Driver\Xdebug');

        $driver = new Stub();
        $this->assertTrue($driver->getDriver() === null);

        $driver->setDriver($mock);
        $this->assertTrue($driver->getDriver() === $mock);
    }

    /**
     * @requires extension xdebug
    public function testStartXdebug()
    {
        $mock = $this->createMock('SebastianBergmann\CodeCoverage\Driver\Xdebug');
        $mock->expects($this->once())
             ->method('start');

        $driver = new Stub();
        $driver->setDriver($mock);
        $driver->start();
    }

    /**
     * @requires extension xdebug
    public function testStopXdebug()
    {
        $mock = $this->createMock('SebastianBergmann\CodeCoverage\Driver\Xdebug');
        $mock->expects($this->once())
             ->method('stop');

        $driver = new Stub();
        $driver->setDriver($mock);
        $driver->stop();
    }

    /**
     * @requires extension phpdbg
    public function testGetterSetterPHPDBG()
    {
        $mock = $this->createMock('SebastianBergmann\CodeCoverage\Driver\PHPDBG');

        $driver = new Stub();
        $this->assertTrue($driver->getDriver() === null);

        $driver->setDriver($mock);
        $this->assertTrue($driver->getDriver() === $mock);
    }

    /**
     * @requires extension phpdbg
    public function testStartPHPDBG()
    {
        $mock = $this->createMock('SebastianBergmann\CodeCoverage\Driver\PHPDBG');
        $mock->expects($this->once())
             ->method('start');

        $driver = new Stub();
        $driver->setDriver($mock);
        $driver->start();
    }

    /**
     * @requires extension phpdbg
    public function testStopPHPDBG()
    {
        $mock = $this->createMock('SebastianBergmann\CodeCoverage\Driver\PHPDBG');
        $mock->expects($this->once())
             ->method('stop');

        $driver = new Stub();
        $driver->setDriver($mock);
        $driver->stop();
    }

}*/

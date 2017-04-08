<?php
/**
 * Code Coverage Driver 
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Driver;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Driver\XCache;

/**
 * XCache driver test
 *
 * @group Unit
 */
class XCacheTest extends TestCase
{
    public function testConstructNoExtensions()
    {
        $this->getMockFunction('extension_loaded', function () {
            return false;
        });

        try {
            new XCache();

            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \SebastianBergmann\CodeCoverage\RuntimeException);
            $this->assertEquals('This driver requires XCache', $e->getMessage());
        }
    }

    public function testConstructXCacheCoverageNotEnabled()
    {
        $function = $this->createMock('VIPSoft\Test\FunctionProxy');
        $function->expects($this->once())
                 ->method('invokeFunction')
                 ->will($this->returnValue(true));

        $this->getMockFunction('extension_loaded', $function);

        $this->getMockFunction('phpversion', function () {
            return '3.1.0';
        });

        $this->getMockFunction('ini_get', function () {
            return false;
        });

        try {
            new XCache();

            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \SebastianBergmann\CodeCoverage\Exception);
            $this->assertEquals('xcache.coverager=On has to be set in php.ini', $e->getMessage());
        }
    }

    public function testConstructXCache()
    {
        $function = $this->createMock('VIPSoft\Test\FunctionProxy');
        $function->expects($this->once())
                 ->method('invokeFunction')
                 ->will($this->returnValue(true));

        $this->getMockFunction('extension_loaded', $function);

        $this->getMockFunction('phpversion', function () {
            return '3.1.0';
        });

        $this->getMockFunction('ini_get', function () {
            return true;
        });

        new XCache();
    }

    public function testStartXCache()
    {
        $function = $this->createMock('VIPSoft\Test\FunctionProxy');
        $function->expects($this->once())
                 ->method('invokeFunction')
                 ->will($this->returnValue(true));

        $this->getMockFunction('extension_loaded', $function);

        $this->getMockFunction('phpversion', function () {
            return '3.1.0';
        });

        $this->getMockFunction('ini_get', function () {
            return true;
        });

        $function = $this->createMock('VIPSoft\Test\FunctionProxy');
        $function->expects($this->once())
                 ->method('invokeFunction');

        $this->getMockFunction('xcache_coverager_start', $function);

        $driver = new XCache();
        $driver->start();
    }

    public function testStopXCache()
    {
        $function = $this->createMock('VIPSoft\Test\FunctionProxy');
        $function->expects($this->once())
                 ->method('invokeFunction')
                 ->will($this->returnValue(true));

        $this->getMockFunction('extension_loaded', $function);

        $this->getMockFunction('phpversion', function () {
            return '3.1.0';
        });

        $this->getMockFunction('ini_get', function () {
            return true;
        });

        $function = $this->createMock('VIPSoft\Test\FunctionProxy');
        $function->expects($this->exactly(2))
                 ->method('invokeFunction');

        $this->getMockFunction('xcache_coverager_get', $function);
        $this->getMockFunction('xcache_coverager_stop', $function);

        $driver = new XCache();
        $driver->stop();
    }
}

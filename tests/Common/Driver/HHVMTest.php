<?php
/**
 * Code Coverage Driver 
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Driver;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Driver\HHVM;

/**
 * HHVM driver test
 *
 * @group Unit
 */
class HHVMTest extends TestCase
{
    public function testConstructNotHHVM()
    {
        $this->getMockFunction('defined', function () {
            return false;
        });

        try {
            new HHVM();

            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \PHP_CodeCoverage_Exception);
            $this->assertEquals('This driver requires HHVM', $e->getMessage());
        }
    }

    public function testConstructXCache()
    {
        $this->getMockFunction('defined', function () {
            return true;
        });

        new HHVM();
    }

    public function testStartXCache()
    {
        $this->getMockFunction('defined', function () {
            return true;
        });

        $this->getMockFunction('fb_enable_code_coverage', null);

        $driver = new HHVM();
        $driver->start();
    }

    public function testStopXCache()
    {
        $this->getMockFunction('defined', function () {
            return true;
        });

        $function = $this->getMock('VIPSoft\Test\FunctionProxy');
        $function->expects($this->exactly(2))
                 ->method('invokeFunction');

        $this->getMockFunction('fb_get_code_coverage', $function);
        $this->getMockFunction('fb_disable_code_coverage', $function);

        $driver = new HHVM();
        $driver->stop();
    }
}

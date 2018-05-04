<?php
/**
 * Clover Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Report\Factory;

/**
 * Clover report test
 *
 * @group Unit
 */
/**
 * TODO - reimplement integration tests'
 *
class CloverTest extends TestCase
{
    public function testProcess()
    {
        $report = $this->getMockBuilder('SebastianBergmann\CodeCoverage\Node\File')
                       ->disableOriginalConstructor()
                       ->getMock();

        $coverage = $this->createMock('SebastianBergmann\CodeCoverage\CodeCoverage');
        $coverage->expects($this->once())
                 ->method('getReport')
                 ->will($this->returnValue($report));

        $report = new Clover(array());
        $result = $report->process($coverage);

        $this->assertTrue(strpos($result, '<?xml version="1.0" encoding="UTF-8"?>') === 0);
    }
}*/

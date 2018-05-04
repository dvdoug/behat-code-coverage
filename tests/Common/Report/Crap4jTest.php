<?php
/**
 * Crap4j Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Report\Factory;
use SebastianBergmann\CodeCoverage\Report\Crap4j;

/**
 * Crap4j report test
 *
 * @group Unit
 */
/**
 * TODO - reimplement integration tests'
 *
class Crap4jTest extends TestCase
{
    /**
     * TODO - reimplement integration tests'
     *

    public function testProcess()
    {
        if ( ! class_exists('SebastianBergmann\CodeCoverage\Report\Crap4j')) {
            $this->markTestSkipped();

            return;
        }

        $report = $this->getMockBuilder('SebastianBergmann\CodeCoverage\Report\Crap4j')
                       ->disableOriginalConstructor()
                       ->getMock();

        $coverage = $this->createMock('SebastianBergmann\CodeCoverage\CodeCoverage');
        $coverage->expects($this->once())
                 ->method('getReport')
                 ->will($this->returnValue($report));

        $report = new Crap4j();
        $result = $report->process($coverage);

        $this->assertTrue(strpos($result, '<?xml version="1.0" encoding="UTF-8"?>') === 0);

    }
}*/

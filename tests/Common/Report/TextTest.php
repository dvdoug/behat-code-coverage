<?php
/**
 * Text Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Report\Factory;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Node\File;

/**
 * Text report test
 *
 * @group Unit
 */
class TextTest extends TestCase
{
    public function testProcess()
    {
        $this->markTestIncomplete(
            'This test seems to be broken after update to phpunit ~4.0.'
        );

        $report = $this->getMockBuilder('File')
                       ->disableOriginalConstructor()
                       ->getMock();

        $coverage = $this->createMock('SebastianBergmann\CodeCoverage\CodeCoverage');
        $coverage->expects($this->once())
                 ->method('getReport')
                 ->will($this->returnValue($report));

        $report = new Text(array());
        ob_start();
        $report->process($coverage);
        $result = ob_get_clean();

        $this->assertTrue(strpos($result, 'Code Coverage Report') !== false);
    }
}

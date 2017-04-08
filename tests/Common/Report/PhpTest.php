<?php
/**
 * PHP Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Report\Factory;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\CodeCoverage;
/**
 * PHP report test
 *
 * @group Unit
 */
class PhpTest extends TestCase
{
    public function testProcess()
    {
        $coverage = $this->createMock('SebastianBergmann\CodeCoverage\CodeCoverage');
        $filter = $this->createMock('SebastianBergmann\CodeCoverage\Filter');
        $filter
            ->expects($this->once())
            ->method('getBlacklistedFiles')
            ->will($this->returnValue(array()));
        $filter
            ->expects($this->once())
            ->method('getWhitelistedFiles')
            ->will($this->returnValue(array()));
        $coverage->expects($this->once())
                  ->method('filter')
                  ->will($this->returnValue($filter));


        $report = new PHP(array());
        $result = $report->process($coverage);

        $this->assertTrue(strncmp($result, '<?php', 2) === 0);
    }
}

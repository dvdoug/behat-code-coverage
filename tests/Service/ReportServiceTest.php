<?php
/**
 * Report Service
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Service;

use VIPSoft\TestCase;

/**
 * Report service test
 *
 * @group Unit
 */
class ReportServiceTest extends TestCase
{
    public function __construct()
    {
        if ( ! class_exists('LeanPHP\Behat\CodeCoverage\Test\PHP_CodeCoverage_Report_HTML')) {
            eval(<<<END_OF_SQLITE
namespace LeanPHP\Behat\CodeCoverage\Test {
    class PHP_CodeCoverage_Report_HTML
    {
        static public \$proxiedMethods;

        public function __call(\$methodName, \$args)
        {
            if (isset(self::\$proxiedMethods[\$methodName])) {
                return call_user_func_array(self::\$proxiedMethods[\$methodName], \$args);
            }
        }
    }
}
END_OF_SQLITE
            );
        }
    }

    public function testGenerateReport()
    {
        $report = $this->getMockBuilder('VIPSoft\CodeCoverageCommon\Report\Html')
                       ->disableOriginalConstructor()
                       ->getMock();

        $factory = $this->getMock('VIPSoft\CodeCoverageCommon\Report\Factory');
        $factory->expects($this->once())
                ->method('create')
                ->will($this->returnValue($report));

        $coverage = $this->getMock('PHP_CodeCoverage');

        $service = new ReportService(array('report' => array('format' => 'html', 'options' => array())), $factory);
        $service->generateReport($coverage);
    }
}

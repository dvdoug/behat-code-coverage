<?php
/**
 * Report Service
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Service;

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
        if ( ! class_exists('VIPSoft\CodeCoverageExtension\Test\PHP_CodeCoverage_Report_HTML')) {
            eval(<<<END_OF_SQLITE
namespace VIPSoft\CodeCoverageExtension\Test {
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
        $coverage = $this->getMockBuilder('PHP_CodeCoverage')
                         ->disableOriginalConstructor()
                         ->getMock();

        $proxy = $this->getMock('VIPSoft\Test\FunctionProxy');
        $proxy->expects($this->once())
              ->method('invokeFunction');

        \VIPSoft\CodeCoverageExtension\Test\PHP_CodeCoverage_Report_HTML::$proxiedMethods['process'] = array($proxy, 'invokeFunction');

        $service = new ReportService(array(
            'report' => array(
                'directory' => '/dev/null',
                'class'     => 'VIPSoft\CodeCoverageExtension\Test\PHP_CodeCoverage_Report_HTML',
            )
        ));

        $service->generateReport($coverage);
    }
}

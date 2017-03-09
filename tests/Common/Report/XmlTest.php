<?php
/**
 * XML Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Report\Factory;

/**
 * XML report test
 *
 * @group Unit
 */
class XmlTest extends TestCase
{
    public function testProcess()
    {
        if ( ! class_exists('PHP_CodeCoverage_Report_XML')) {
            $this->markTestSkipped();

            return;
        }

        $this->markTestIncomplete();
    }
}

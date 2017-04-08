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
use SebastianBergmann\CodeCoverage\Report\XML;

/**
 * XML report test
 *
 * @group Unit
 */
class XmlTest extends TestCase
{
    public function testProcess()
    {
        if ( ! class_exists('XML')) {
            $this->markTestSkipped();

            return;
        }

        $this->markTestIncomplete();
    }
}

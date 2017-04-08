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
class Crap4jTest extends TestCase
{
    public function testProcess()
    {
        if ( ! class_exists('SebastianBergmann\CodeCoverage\Report\Crap4j')) {
            $this->markTestSkipped();

            return;
        }

        $this->markTestIncomplete();
    }
}

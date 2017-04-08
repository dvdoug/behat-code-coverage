<?php
/**
 * Code Coverage Report Factory
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Report\Factory;

/**
 * Factory test
 *
 * @group Unit
 */
class FactoryTest extends TestCase
{
    /**
     * @dataProvider legacyCreateProvider
     */
    public function testLegacyCreate($expected, $reportType)
    {
        $factory = new Factory();

        $this->assertEquals($expected, get_class($factory->create($reportType, array())));
    }

    public function legacyCreateProvider()
    {
        return array(
            array(
                'LeanPHP\Behat\CodeCoverage\Common\Report\Clover',
                'clover',
            ),
            array(
                'LeanPHP\Behat\CodeCoverage\Common\Report\Html',
                'html',
            ),
            array(
                'LeanPHP\Behat\CodeCoverage\Common\Report\Php',
                'php',
            ),
            array(
                'LeanPHP\Behat\CodeCoverage\Common\Report\Text',
                'text',
            ),
        );
    }

    /**
     * @dataProvider createProvider
     */
    public function testCreate($expected, $reportType)
    {
        $factory = new Factory();

        try {
            $this->assertEquals($expected, get_class($factory->create($reportType, array())));
        } catch (\Exception $e) {
            $this->assertTrue(strpos($e->getMessage(), 'requires PHP_CodeCoverage 4.0+') !== false);
        }
    }

    public function createProvider()
    {
        return array(
            array(
                'LeanPHP\Behat\CodeCoverage\Common\Report\Crap4j',
                'crap4j',
            ),
            array(
                'LeanPHP\Behat\CodeCoverage\Common\Report\Xml',
                'xml',
            ),
        );
    }

    public function testCreateInvalid()
    {
        $factory = new Factory();

        $this->assertTrue($factory->create('HTML', array()) === null);
    }
}

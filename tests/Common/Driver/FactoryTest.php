<?php
/**
 * Code Coverage Driver Factory
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Driver;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Driver\Factory;

/**
 * Driver factory test
 *
 * @group Unit
 */
class FactoryTest extends TestCase
{
    public function testCreateNoClasses()
    {
        $factory = new Factory(array());

        $driver = $factory->create();

        $this->assertTrue($driver === null);
    }

    public function testCreate()
    {
        if ( ! class_exists('LeanPHP\Behat\CodeCoverage\Common\Driver\Factory\GoodDriver')) {
            eval(<<<END_OF_CLASS_DEFINITION
namespace LeanPHP\Behat\CodeCoverage\Common\Driver\Factory {
    class GoodDriver implements \PHP_CodeCoverage_Driver
    {
        public function __construct()
        {
        }

        public function start()
        {
        }

        public function stop()
        {
        }
    }
}
END_OF_CLASS_DEFINITION
            );
        }

        $classes = array(
            'LeanPHP\Behat\CodeCoverage\Common\Driver\Factory\GoodDriver',
        );

        $factory = new Factory($classes);

        $driver = $factory->create();

        $this->assertTrue($driver !== null);
    }

    public function testCreateException()
    {
        if ( ! class_exists('LeanPHP\Behat\CodeCoverage\Common\Driver\Factory\BadDriver')) {
            eval(<<<END_OF_CLASS_DEFINITION
namespace LeanPHP\Behat\CodeCoverage\Common\Driver\Factory {
    class BadDriver implements \PHP_CodeCoverage_Driver
    {
        public function __construct()
        {
            throw new \Exception('bad');
        }

        public function start()
        {
        }

        public function stop()
        {
        }
    }
}
END_OF_CLASS_DEFINITION
            );
        }

        $classes = array(
            'LeanPHP\Behat\CodeCoverage\Common\Driver\Factory\BadDriver',
        );

        $factory = new Factory($classes);

        $driver = $factory->create();

        $this->assertTrue($driver === null);
    }
}

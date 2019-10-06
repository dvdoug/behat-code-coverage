<?php

declare(strict_types=1);
/**
 * Code Coverage Driver Factory.
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Common\Driver;

use PHPUnit\Framework\TestCase;

/**
 * Driver factory test.
 *
 * @group Unit
 */
class FactoryTest extends TestCase
{
    public function testCreateNoClasses(): void
    {
        $factory = new Factory([]);

        $driver = $factory->create();

        $this->assertTrue($driver === null);
    }

    public function testCreate(): void
    {
        if (!class_exists('DVDoug\Behat\CodeCoverage\Common\Driver\Factory\GoodDriver')) {
            eval(<<<END_OF_CLASS_DEFINITION
                namespace DVDoug\Behat\CodeCoverage\Common\Driver\Factory {
                    class GoodDriver implements \SebastianBergmann\CodeCoverage\Driver\Driver
                    {
                        public function __construct()
                        {
                        }
                
                        public function start(bool \$determineUnusedAndDead = true): void
                        {
                        }
                
                        public function stop(): array
                        {
                        }
                    }
                }
                END_OF_CLASS_DEFINITION
            );
        }

        $classes = [
            'DVDoug\Behat\CodeCoverage\Common\Driver\Factory\GoodDriver',
        ];

        $factory = new Factory($classes);

        $driver = $factory->create();

        $this->assertTrue($driver !== null);
    }

    public function testCreateException(): void
    {
        if (!class_exists('DVDoug\Behat\CodeCoverage\Common\Driver\Factory\BadDriver')) {
            eval(<<<END_OF_CLASS_DEFINITION
                namespace DVDoug\Behat\CodeCoverage\Common\Driver\Factory {
                    class BadDriver implements \SebastianBergmann\CodeCoverage\Driver\Driver
                    {
                        public function __construct()
                        {
                            throw new \Exception('bad');
                        }
                
                        public function start(bool \$determineUnusedAndDead = true): void
                        {
                        }
                
                        public function stop(): array
                        {
                        }
                    }
                }
                END_OF_CLASS_DEFINITION
            );
        }

        $classes = [
            'DVDoug\Behat\CodeCoverage\Common\Driver\Factory\BadDriver',
        ];

        $factory = new Factory($classes);

        $driver = $factory->create();

        $this->assertTrue($driver === null);
    }
}

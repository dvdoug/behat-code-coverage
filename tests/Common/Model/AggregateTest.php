<?php

declare(strict_types=1);
/**
 * Code Coverage Aggregator.
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Common\Model;

use PHPUnit\Framework\TestCase;

/**
 * Aggregator test.
 *
 * @group Unit
 */
class AggregateTest extends TestCase
{
    public function testGetCoverage(): void
    {
        $aggregate = new Aggregate();

        $coverage = $aggregate->getCoverage();

        $this->assertTrue(is_array($coverage));
        $this->assertCount(0, $coverage);
    }

    public function testUpdateWhenEmpty(): void
    {
        $aggregate = new Aggregate();
        $aggregate->update('test', [2 => 1]);

        $coverage = $aggregate->getCoverage();

        $this->assertTrue(is_array($coverage));
        $this->assertCount(1, $coverage);
        $this->assertEquals([2 => 1], $coverage['test']);
    }

    public function testUpdateForCollision(): void
    {
        $aggregate = new Aggregate();
        $aggregate->update('first', [2 => 1]);
        $aggregate->update('second', [1 => -1]);

        $coverage = $aggregate->getCoverage();

        $this->assertTrue(is_array($coverage));
        $this->assertCount(2, $coverage);
        $this->assertEquals([2 => 1], $coverage['first']);
        $this->assertEquals([1 => -1], $coverage['second']);
    }

    /**
     * @dataProvider updateDataProvider
     */
    public function testUpdate($first, $second, $expected): void
    {
        $aggregate = new Aggregate();
        $aggregate->update('test', $first);
        $aggregate->update('test', $second);

        $coverage = $aggregate->getCoverage();

        $this->assertTrue(is_array($coverage));
        $this->assertCount(1, $coverage);
        $this->assertEquals($expected, $coverage['test']);
    }

    public function updateDataProvider()
    {
        return [
            [
                [1 => 1],
                [1 => 1],
                [1 => 1],
            ],
            [
                [1 => 1],
                [2 => 1],
                [1 => 1, 2 => 1],
            ],
            [
                [1 => -1],
                [1 => 1],
                [1 => 1],
            ],
            [
                [1 => 1],
                [1 => -1],
                [1 => 1],
            ],
        ];
    }
}

<?php
/**
 * Code Coverage Aggregator
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Model;

use VIPSoft\TestCase;

/**
 * Aggregator test
 *
 * @group Unit
 */
class AggregateTest extends TestCase
{
    public function testGetCoverage()
    {
        $aggregate = new Aggregate();

        $coverage = $aggregate->getCoverage();

        $this->assertTrue(is_array($coverage));
        $this->assertCount(0, $coverage);
    }

    public function testUpdateWhenEmpty()
    {
        $aggregate = new Aggregate();
        $aggregate->update('test', array(2 => 1));

        $coverage = $aggregate->getCoverage();

        $this->assertTrue(is_array($coverage));
        $this->assertCount(1, $coverage);
        $this->assertEquals(array(2 => 1), $coverage['test']);
    }

    public function testUpdateForCollision()
    {
        $aggregate = new Aggregate();
        $aggregate->update('first', array(2 => 1));
        $aggregate->update('second', array(1 => -1));

        $coverage = $aggregate->getCoverage();

        $this->assertTrue(is_array($coverage));
        $this->assertCount(2, $coverage);
        $this->assertEquals(array(2 => 1), $coverage['first']);
        $this->assertEquals(array(1 => -1), $coverage['second']);
    }

    /**
     * @dataProvider updateDataProvider
     */
    public function testUpdate($first, $second, $expected)
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
        return array(
            array(
                array(1 => 1),
                array(1 => 1),
                array(1 => 1),
            ),
            array(
                array(1 => 1),
                array(2 => 1),
                array(1 => 1, 2 => 1),
            ),
            array(
                array(1 => -1),
                array(1 => 1),
                array(1 => 1),
            ),
            array(
                array(1 => 1),
                array(1 => -1),
                array(1 => 1),
            ),
        );
    }
}

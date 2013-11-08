<?php
/**
 * Code Coverage Aggergator
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Model;

use VIPSoft\TestCase;

/**
 * @group Unit
 */
class AggregateTest extends TestCase
{
    public function testGetCoverage()
    {
        $aggregate = new Aggregate();

        $coverage = $aggregate->getCoverage();

        $this->assertTrue(is_array($coverage));
        $this->assertEquals(0, count($coverage));
    }

    public function testUpdateWhenEmpty()
    {
        $aggregate = new Aggregate();
        $aggregate->update('test', array(2 => 1));

        $coverage = $aggregate->getCoverage();

        $this->assertTrue(is_array($coverage));
        $this->assertEquals(1, count($coverage));
        $this->assertEquals(array(2 => 1), $coverage['test']);
    }

    public function testUpdateForCollision()
    {
        $aggregate = new Aggregate();
        $aggregate->update('first', array(2 => 1));
        $aggregate->update('second', array(1 => -1));

        $coverage = $aggregate->getCoverage();

        $this->assertTrue(is_array($coverage));
        $this->assertEquals(2, count($coverage));
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
        $this->assertEquals(1, count($coverage));
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

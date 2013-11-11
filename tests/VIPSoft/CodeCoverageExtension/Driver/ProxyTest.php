<?php
/**
 * Code Coverage Driver 
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension;

use VIPSoft\TestCase;
use VIPSoft\CodeCoverageExtension\Driver\Proxy;

/**
 * Proxy driver test
 *
 * @group Unit
 */
class ProxyTest extends TestCase
{
    protected function setUp()
    {
        $this->config = array(
            'drivers' => array(
                'remote',
                'local',
            )
        );

        $this->coverage = $this->getMockBuilder('PHP_CodeCoverage_Driver')
                               ->disableOriginalConstructor()
                               ->getMock();

        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->container->expects($this->any())
                        ->method('get')
                        ->will($this->returnValue($this->coverage));
    }

    public function testStart()
    {
        $this->coverage->expects($this->exactly(2))
                       ->method('start');

        $driver = new Proxy($this->config, $this->container);
        $driver->start();
    }

    public function testStop()
    {
        $this->coverage->expects($this->exactly(2))
                       ->method('stop')
                       ->will($this->returnValue(
                           array(
                               'SomeClass' => array(
                                   1 => 1,
                                   2 => -1,
                                   3 => -2,
                               )
                           )
                       ));

        $driver   = new Proxy($this->config, $this->container);
        $coverage = $driver->stop();

        $this->assertEquals(
            array(
                'SomeClass' => array(
                    1 => 1,
                    2 => -1,
                    3 => -2,
                )
            ),
            $coverage
        );
    }
}

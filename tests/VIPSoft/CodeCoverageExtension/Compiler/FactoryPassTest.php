<?php
/**
 * Factory Compiler Pass
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Compiler;

use VIPSoft\TestCase;
use VIPSoft\CodeCoverageExtension\Compiler\FactoryPass;

/**
 * Factory compiler pass test
 *
 * @group Unit
 */
class FactoryPassTest extends TestCase
{
    public function testProcessNoServiceDefinition()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->expects($this->once())
                  ->method('hasDefinition')
                  ->will($this->returnValue(false));

        $pass = new FactoryPass();
        $pass->process($container);
    }

    public function testProcess()
    {
        $factory = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $factory->expects($this->once())
                ->method('setArguments');

        $xcache = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $xcache->expects($this->once())
               ->method('getClass')
               ->will($this->returnValue('VIPSoft\CodeCoverageCommon\Driver\XCache'));

        $xdebug = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $xdebug->expects($this->once())
               ->method('getClass')
               ->will($this->returnValue('PHP_CodeCoverage_Driver_Xdebug'));

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $container->expects($this->at(0))
                  ->method('hasDefinition')
                  ->with('vipsoft.code_coverage.driver.factory')
                  ->will($this->returnValue(true));

        $container->expects($this->at(1))
                  ->method('getDefinition')
                  ->with('vipsoft.code_coverage.driver.factory')
                  ->will($this->returnValue($factory));

        $container->expects($this->at(2))
                  ->method('findTaggedServiceIds')
                  ->with('vipsoft.code_coverage.driver')
                  ->will($this->returnValue(array(
                      'vipsoft.code_coverage.driver.xcache' => array(),
                      'vipsoft.code_coverage.driver.xdebug' => array(),
                  )));

        $container->expects($this->at(3))
                  ->method('getDefinition')
                  ->with('vipsoft.code_coverage.driver.xcache')
                  ->will($this->returnValue($xcache));

        $container->expects($this->at(4))
                  ->method('getDefinition')
                  ->with('vipsoft.code_coverage.driver.xdebug')
                  ->will($this->returnValue($xdebug));

        $pass = new FactoryPass();
        $pass->process($container);
    }
}

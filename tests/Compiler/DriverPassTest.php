<?php
/**
 * Driver Compiler Pass
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Compiler;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Compiler\DriverPass;

/**
 * Driver compiler pass test
 *
 * @group Unit
 */
class DriverPassTest extends TestCase
{
    public function testProcessNoServiceDefinition()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->expects($this->once())
                  ->method('hasDefinition')
                  ->will($this->returnValue(false));

        $pass = new DriverPass();
        $pass->process($container);
    }

    public function testProcess()
    {
        $proxy = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $proxy->expects($this->exactly(2))
              ->method('addMethodCall');

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->expects($this->once())
                  ->method('hasDefinition')
                  ->with('behat.code_coverage.driver.proxy')
                  ->will($this->returnValue(true));

        $container->expects($this->once())
                  ->method('getDefinition')
                  ->with('behat.code_coverage.driver.proxy')
                  ->will($this->returnValue($proxy));

        $container->expects($this->once())
                  ->method('getParameter')
                  ->with('behat.code_coverage.config.drivers')
                  ->will($this->returnValue(array('local', 'remote')));

        $container->expects($this->once())
                  ->method('findTaggedServiceIds')
                  ->with('behat.code_coverage.driver')
                  ->will($this->returnValue(array(
                      'behat.code_coverage.driver.local' => array(array(
                          'alias' => 'local'
                      )),
                      'behat.code_coverage.driver.remote' => array(array(
                          'alias' => 'remote'
                      )),
                  )));

        $pass = new DriverPass();
        $pass->process($container);
    }
}

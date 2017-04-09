<?php
/**
 * Filter Compiler Pass
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Compiler;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Compiler\FilterPass;

/**
 * Filter compiler pass test
 *
 * @group Unit
 */
class FilterPassTest extends TestCase
{
    public function testProcessNoServiceDefinition()
    {
        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->expects($this->exactly(2))
                  ->method('hasDefinition')
                  ->will($this->returnValue(false));

        $pass = new FilterPass();
        $pass->process($container);
    }

    public function testProcessCodeCoverage()
    {
        $coverage = $this->createMock('Symfony\Component\DependencyInjection\Definition');
        $coverage->expects($this->exactly(4))
                 ->method('addMethodCall');

        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->expects($this->exactly(2))
                  ->method('hasDefinition')
                  ->will($this->onConsecutiveCalls(true, false));

        $container->expects($this->once())
                  ->method('getDefinition')
                  ->with('behat.code_coverage.php_code_coverage')
                  ->will($this->returnValue($coverage));

        $container->expects($this->once())
                  ->method('getParameter')
                  ->with('behat.code_coverage.config.filter')
                  ->will($this->returnValue(array(
                      'whitelist' => array(
                          'addUncoveredFilesFromWhitelist' => false,
                          'processUncoveredFilesFromWhitelist' => true
                      ),
                      'forceCoversAnnotation' => false,
                      'mapTestClassNameToCoveredClassName' => true
                  )));

        $pass = new FilterPass();
        $pass->process($container);
    }

    public function testProcessCodeCoverageFilter()
    {
        $filter = $this->createMock('Symfony\Component\DependencyInjection\Definition');
        $filter->expects($this->exactly(4))
               ->method('addMethodCall');

        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->expects($this->exactly(2))
                  ->method('hasDefinition')
                  ->will($this->onConsecutiveCalls(false, true));

        $container->expects($this->once())
                  ->method('getDefinition')
                  ->with('behat.code_coverage.php_code_coverage_filter')
                  ->will($this->returnValue($filter));

        $container->expects($this->once())
                  ->method('getParameter')
                  ->with('behat.code_coverage.config.filter')
                  ->will($this->returnValue(array(
                      'whitelist' => array(
                          'addUncoveredFilesFromWhitelist' => false,
                          'processUncoveredFilesFromWhitelist' => true,
                          'include' => array(
                              'directories' => array(
                                  'directory1' => array(
                                      'prefix' => 'Secure',
                                      'suffix' => '.php',
                                  )
                              ),
                              'files' => array(
                                  'file1'
                              ),
                          ),
                          'exclude' => array(
                              'directories' => array(
                                  'directory2' => array(
                                      'prefix' => 'Insecure',
                                      'suffix' => '.inc',
                                  )
                              ),
                              'files' => array(
                                  'file2'
                              ),
                          ),
                      ),
                      'blacklist' => array(
                          'include' => array(
                              'directories' => array(
                                  'directory3' => array(
                                      'prefix' => 'Public',
                                      'suffix' => '.php',
                                  )
                              ),
                              'files' => array(
                                  'file3'
                              ),
                          ),
                          'exclude' => array(
                              'directories' => array(
                                  'directory4' => array(
                                      'prefix' => 'Private',
                                      'suffix' => '.inc',
                                  )
                              ),
                              'files' => array(
                                  'file4'
                              ),
                          ),
                      ),
                  )));

        $pass = new FilterPass();
        $pass->process($container);
    }
}

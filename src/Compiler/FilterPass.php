<?php
/**
 * Filter Compiler Pass
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Filter pass - filter configuration
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class FilterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processCodeCoverage($container);
        $this->processCodeCoverageFilter($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function processCodeCoverage(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('behat.code_coverage.php_code_coverage')) {
            return;
        }

        $coverage = $container->getDefinition('behat.code_coverage.php_code_coverage');
        $config   = $container->getParameter('behat.code_coverage.config.filter');

        $coverage->addMethodCall(
            'setAddUncoveredFilesFromWhitelist',
            array($config['whitelist']['addUncoveredFilesFromWhitelist'])
        );
        $coverage->addMethodCall(
            'setProcessUncoveredFilesFromWhiteList',
            array($config['whitelist']['processUncoveredFilesFromWhitelist'])
        );
        $coverage->addMethodCall(
            'setForceCoversAnnotation',
            array($config['forceCoversAnnotation'])
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function processCodeCoverageFilter(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('behat.code_coverage.php_code_coverage_filter')) {
            return;
        }

        $filter = $container->getDefinition('behat.code_coverage.php_code_coverage_filter');
        $config = $container->getParameter('behat.code_coverage.config.filter');

        $dirs = array(
            'addDirectoryToWhiteList' => array('whitelist', 'include', 'directories'),
            'removeDirectoryFromWhiteList' => array('whitelist', 'exclude', 'directories'),
        );

        foreach ($dirs as $method => $hiera) {
            foreach ($config[$hiera[0]][$hiera[1]][$hiera[2]] as $path => $dir) {
                $filter->addMethodCall($method, array($path, $dir['suffix'], $dir['prefix']));
            }
        }

        $files = array(
            'addFileToWhiteList' => array('whitelist', 'include', 'files'),
            'removeFileFromWhiteList' => array('whitelist', 'exclude', 'files'),
        );

        foreach ($files as $method => $hiera) {
            foreach ($config[$hiera[0]][$hiera[1]][$hiera[2]] as $file) {
                $filter->addMethodCall($method, array($file));
            }
        }
    }
}

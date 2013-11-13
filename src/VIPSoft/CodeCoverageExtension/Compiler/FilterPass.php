<?php
/**
 * Filter Compiler Pass
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Filter pass - configure the PHP_CodeCoverage_Filter object using the provided configuration
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class FilterPass implements CompilerPassInterface
{
    /**
     * Processes container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
    }
}

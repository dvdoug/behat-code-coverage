<?php
/**
 * Factory Compiler Pass
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Factory pass - register available drivers
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class FactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('vipsoft.code_coverage.driver.factory')) {
            return;
        }

        $factory = $container->getDefinition('vipsoft.code_coverage.driver.factory');
        $drivers = array();
        $ids     = $container->findTaggedServiceIds('vipsoft.code_coverage.driver');

        foreach ($ids as $id => $attributes) {
            $drivers[] = $container->getDefinition($id)->getClass();
        }

        $factory->setArguments(array($drivers));
    }
}

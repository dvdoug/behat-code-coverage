<?php
/**
 * Driver Compiler Pass
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
 * Driver pass - register only the enabled drivers
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class DriverPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('behat.code_coverage.driver.proxy')) {
            return;
        }

        $proxy = $container->getDefinition('behat.code_coverage.driver.proxy');
        $enabled = $container->getParameter('behat.code_coverage.config.drivers');

        foreach ($container->findTaggedServiceIds('behat.code_coverage.driver') as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                if (isset($attributes['alias'])
                    && in_array($attributes['alias'], $enabled)
                ) {
                    $proxy->addMethodCall('addDriver', array(new Reference($id)));
                }
            }
        }
    }
}

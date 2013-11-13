<?php
/**
 * Code Coverage Extension for Behat
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension;

use Behat\Behat\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use VIPSoft\CodeCoverageExtension\Compiler;

/**
 * Code coverage extension
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Extension implements ExtensionInterface
{
    /**
     * @var string
     */
    private $configFolder;

    /**
     * Constructor
     *
     * @param string $configFolder
     */
    public function __construct($configFolder = null)
    {
        $this->configFolder = $configFolder ?: __DIR__ . '/Resources/config';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator($this->configFolder));
        $loader->load('services.xml');

        if ( ! isset($config['auth']['user']) || ! isset($config['auth']['password'])) {
            $config['auth'] = null;
        }

        if ( ! count($config['drivers'])) {
            $config['drivers'] = array('remote', 'local');
        }

        if ( ! count($config['report']['options'])) {
            $config['report']['options'] = array(
                'target' => '/tmp'
            );
        }

        $container->setParameter('behat.code_coverage.config.auth', $config['auth']);
        $container->setParameter('behat.code_coverage.config.create', $config['create']);
        $container->setParameter('behat.code_coverage.config.read', $config['read']);
        $container->setParameter('behat.code_coverage.config.delete', $config['delete']);
        $container->setParameter('behat.code_coverage.config.drivers', $config['drivers']);
        $container->setParameter('behat.code_coverage.config.filter', $config['filter']);
        $container->setParameter('behat.code_coverage.config.report', $config['report']);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder->
            children()->
                arrayNode('auth')->
                    children()->
                        scalarNode('user')->end()->
                        scalarNode('password')->end()->
                    end()->
                end()->
                arrayNode('create')->
                    addDefaultsIfNotSet()->
                    children()->
                        scalarNode('method')->defaultValue('POST')->end()->
                        scalarNode('path')->defaultValue('/')->end()->
                    end()->
                end()->
                arrayNode('read')->
                    addDefaultsIfNotSet()->
                    children()->
                        scalarNode('method')->defaultValue('GET')->end()->
                        scalarNode('path')->defaultValue('/')->end()->
                    end()->
                end()->
                arrayNode('delete')->
                    addDefaultsIfNotSet()->
                    children()->
                        scalarNode('method')->defaultValue('DELETE')->end()->
                        scalarNode('path')->defaultValue('/')->end()->
                    end()->
                end()->
                arrayNode('drivers')->
                    prototype('scalar')->end()->
                end()->
                arrayNode('filter')->
                    addDefaultsIfNotSet()->
                    children()->
                        scalarNode('forceCoversAnnotation')->
                            defaultFalse()->
                        end()->
                        scalarNode('mapTestClassNameToCoveredClassName')->
                            defaultFalse()->
                        end()->
                        arrayNode('whitelist')->
                            addDefaultsIfNotSet()->
                            children()->
                                scalarNode('addUncoveredFilesFromWhitelist')->
                                    defaultTrue()->
                                end()->
                                scalarNode('processUncoveredFilesFromWhitelist')->
                                    defaultFalse()->
                                end()->
                                arrayNode('include')->
                                    addDefaultsIfNotSet()->
                                    children()->
                                        arrayNode('directories')->
                                           useAttributeAsKey('name')->
                                           prototype('array')->
                                               children()->
                                                   scalarNode('prefix')->defaultValue('')->end()->
                                                   scalarNode('suffix')->defaultValue('.php')->end()->
                                               end()->
                                           end()->
                                        end()->
                                        arrayNode('files')->
                                           prototype('scalar')->end()->
                                        end()->
                                    end()->
                                end()->
                                arrayNode('exclude')->
                                    addDefaultsIfNotSet()->
                                    children()->
                                        arrayNode('directories')->
                                           useAttributeAsKey('name')->
                                           prototype('array')->
                                               children()->
                                                   scalarNode('prefix')->defaultValue('')->end()->
                                                   scalarNode('suffix')->defaultValue('.php')->end()->
                                               end()->
                                           end()->
                                        end()->
                                        arrayNode('files')->
                                           prototype('scalar')->end()->
                                        end()->
                                    end()->
                                end()->
                            end()->
                        end()->
                        arrayNode('blacklist')->
                            addDefaultsIfNotSet()->
                            children()->
                                arrayNode('include')->
                                    addDefaultsIfNotSet()->
                                    children()->
                                        arrayNode('directories')->
                                           useAttributeAsKey('name')->
                                           prototype('array')->
                                               children()->
                                                   scalarNode('prefix')->defaultValue('')->end()->
                                                   scalarNode('suffix')->defaultValue('.php')->end()->
                                               end()->
                                           end()->
                                        end()->
                                        arrayNode('files')->
                                           prototype('scalar')->end()->
                                        end()->
                                    end()->
                                end()->
                                arrayNode('exclude')->
                                    addDefaultsIfNotSet()->
                                    children()->
                                        arrayNode('directories')->
                                           useAttributeAsKey('name')->
                                           prototype('array')->
                                               children()->
                                                   scalarNode('prefix')->defaultValue('')->end()->
                                                   scalarNode('suffix')->defaultValue('.php')->end()->
                                               end()->
                                           end()->
                                        end()->
                                        arrayNode('files')->
                                           prototype('scalar')->end()->
                                        end()->
                                    end()->
                                end()->
                            end()->
                        end()->
                    end()->
                end()->
                arrayNode('report')->
                    children()->
                        scalarNode('format')->defaultValue('html')->end()->
                        arrayNode('options')->
                            useAttributeAsKey('name')->
                            prototype('scalar')->end()->
                        end()->
                    end()->
                end()->
            end()->
        end();
    }

    /**
     * {@inheritdoc}
     */
    public function getCompilerPasses()
    {
        return array(
            new Compiler\DriverPass(),
            new Compiler\FilterPass(),
        );
    }
}

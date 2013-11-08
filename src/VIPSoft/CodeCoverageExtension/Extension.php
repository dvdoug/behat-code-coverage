<?php
/**
 * Code Coverage Extension for Behat
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Behat\Behat\Extension\ExtensionInterface;

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
     * @var string $configFolder
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

        if (isset($config['auth']) && isset($config['auth']['user']) && isset($config['auth']['password'])) {
            $container->setParameter('behat.code_coverage.auth', $config['auth']);
        } else {
            $container->setParameter('behat.code_coverage.auth', null);
        }

        if (isset($config['create'])) {
            $container->setParameter('behat.code_coverage.create', $config['create']);
        } else {
            $container->setParameter('behat.code_coverage.create', array(
                'method' => 'POST',
                'path'   => '/',
            ));
        }

        if (isset($config['read'])) {
            $container->setParameter('behat.code_coverage.read', $config['read']);
        } else {
            $container->setParameter('behat.code_coverage.read', array(
                'method' => 'GET',
                'path'   => '/',
            ));
        }

        if (isset($config['delete'])) {
            $container->setParameter('behat.code_coverage.delete', $config['delete']);
        } else {
            $container->setParameter('behat.code_coverage.delete', array(
                'method' => 'DELETE',
                'path'   => '/',
            ));
        }

        if (isset($config['drivers']) && is_array($config['drivers']) && count($config['drivers'])) {
            $container->setParameter('behat.code_coverage.drivers', $config['drivers']);
        } else {
            $container->setParameter('behat.code_coverage.drivers', array('remote', 'local'));
        }

        if (isset($config['output_directory'])) {
            $container->setParameter('behat.code_coverage.output_directory', $config['output_directory']);
        } else {
            $container->setParameter('behat.code_coverage.output_directory', '/tmp');
        }
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
                    defaultNull()->
                end()->
                arrayNode('create')->
                    children()->
                        scalarNode('method')->end()->
                        scalarNode('path')->end()->
                    end()->
                end()->
                arrayNode('read')->
                    children()->
                        scalarNode('method')->end()->
                        scalarNode('path')->end()->
                    end()->
                end()->
                arrayNode('delete')->
                    children()->
                        scalarNode('method')->end()->
                        scalarNode('path')->end()->
                    end()->
                end()->
                arrayNode('drivers')->
                    prototype('scalar')->end()->
                    defaultNull()->
                end()->
                scalarNode('output_directory')->
                    defaultNull()->
                end()->
            end()->
        end();
    }

    /**
     * {@inheritdoc}
     */
    public function getCompilerPasses()
    {
        return array();
    }
}

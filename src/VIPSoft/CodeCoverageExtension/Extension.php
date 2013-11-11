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

        if (isset($config['auth']) && isset($config['auth']['user']) && isset($config['auth']['password'])) {
            $container->setParameter('behat.code_coverage.config.auth', $config['auth']);
        } else {
            $container->setParameter('behat.code_coverage.config.auth', null);
        }

        if (isset($config['create'])) {
            $container->setParameter('behat.code_coverage.config.create', $config['create']);
        } else {
            $container->setParameter('behat.code_coverage.config.create', array(
                'method' => 'POST',
                'path'   => '/',
            ));
        }

        if (isset($config['read'])) {
            $container->setParameter('behat.code_coverage.config.read', $config['read']);
        } else {
            $container->setParameter('behat.code_coverage.config.read', array(
                'method' => 'GET',
                'path'   => '/',
            ));
        }

        if (isset($config['delete'])) {
            $container->setParameter('behat.code_coverage.config.delete', $config['delete']);
        } else {
            $container->setParameter('behat.code_coverage.config.delete', array(
                'method' => 'DELETE',
                'path'   => '/',
            ));
        }

        if (isset($config['drivers']) && is_array($config['drivers']) && count($config['drivers'])) {
            $container->setParameter('behat.code_coverage.config.drivers', $config['drivers']);
        } else {
            $container->setParameter('behat.code_coverage.config.drivers', array('remote', 'local'));
        }

        if (isset($config['filter'])) {
            $container->setParameter('behat.code_coverage.config.filter', $config['filter']);
        } else {
            $container->setParameter('behat.code_coverage.config.filter', null);
        }

        if (isset($config['report'])) {
            $container->setParameter('behat.code_coverage.config.report', $config['report']);
        } else {
            $container->setParameter('behat.code_coverage.config.report', array(
                'class'     => '\PHP_CodeCoverage_Report_HTML',
                'directory' => '/tmp/report',
            ));
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
                end()->
                arrayNode('filter')->
                    prototype('variable')->end()->
                end()->
                arrayNode('report')->
                    children()->
                        scalarNode('class')->end()->
                        scalarNode('directory')->end()->
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
        return array();
    }
}

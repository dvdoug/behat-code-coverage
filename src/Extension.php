<?php

declare(strict_types=1);
/**
 * Code Coverage Extension for Behat.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-2-Clause
 */

namespace DVDoug\Behat\CodeCoverage;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Code coverage extension.
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
     * Constructor.
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
    public function initialize(ExtensionManager $extensionManager): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config): void
    {
        $loader = new XmlFileLoader($container, new FileLocator($this->configFolder));

        $servicesFile = 'services.xml';
        $loader->load($servicesFile);

        if (!isset($config['auth']['user']) || !isset($config['auth']['password'])) {
            $config['auth'] = null;
        }

        if (!count($config['drivers'])) {
            $config['drivers'] = ['local'];
        }

        if (!count($config['report']['options'])) {
            $config['report']['options'] = [
                'target' => '/tmp',
            ];
        }

        if (!$container->hasParameter('mink.base_url')) {
            $container->setParameter('mink.base_url', null);
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
    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('auth')
                    ->children()
                        ->scalarNode('user')->end()
                        ->scalarNode('password')->end()
                    ->end()
                ->end()
                ->arrayNode('create')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('method')->defaultValue('POST')->end()
                        ->scalarNode('path')->defaultValue('/')->end()
                    ->end()
                ->end()
                ->arrayNode('read')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('method')->defaultValue('GET')->end()
                        ->scalarNode('path')->defaultValue('/')->end()
                    ->end()
                ->end()
                ->arrayNode('delete')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('method')->defaultValue('DELETE')->end()
                        ->scalarNode('path')->defaultValue('/')->end()
                    ->end()
                ->end()
                ->arrayNode('drivers')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('filter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('forceCoversAnnotation')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('mapTestClassNameToCoveredClassName')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('whitelist')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('addUncoveredFilesFromWhitelist')
                                    ->defaultTrue()
                                ->end()
                                ->scalarNode('processUncoveredFilesFromWhitelist')
                                    ->defaultFalse()
                                ->end()
                                ->arrayNode('include')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('directories')
                                           ->useAttributeAsKey('name')
                                           ->normalizeKeys(false)
                                           ->prototype('array')
                                               ->children()
                                                   ->scalarNode('prefix')->defaultValue('')->end()
                                                   ->scalarNode('suffix')->defaultValue('.php')->end()
                                               ->end()
                                           ->end()
                                        ->end()
                                        ->arrayNode('files')
                                           ->prototype('scalar')->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('exclude')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('directories')
                                           ->useAttributeAsKey('name')
                                           ->normalizeKeys(false)
                                           ->prototype('array')
                                               ->children()
                                                   ->scalarNode('prefix')->defaultValue('')->end()
                                                   ->scalarNode('suffix')->defaultValue('.php')->end()
                                               ->end()
                                           ->end()
                                        ->end()
                                        ->arrayNode('files')
                                           ->prototype('scalar')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('report')
                    ->children()
                        ->scalarNode('format')->defaultValue('html')->end()
                        ->arrayNode('options')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'code_coverage';
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $input = $container->get('cli.input');
        if ($input->hasParameterOption('--no-coverage')) {
            $container->getParameterBag()->set('behat.code_coverage.skip', true);
        }

        $this->setupDriver($container);
        $this->setupFactory($container);
        $this->setupCodeCoverage($container);
        $this->setupCodeCoverageFilter($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function setupDriver(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('behat.code_coverage.driver.proxy')) {
            return;
        }

        $proxy = $container->getDefinition('behat.code_coverage.driver.proxy');
        $enabled = $container->getParameter('behat.code_coverage.config.drivers');

        foreach ($container->findTaggedServiceIds('behat.code_coverage.driver') as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                if (isset($attributes['alias'])
                    && in_array($attributes['alias'], $enabled)
                ) {
                    $proxy->addMethodCall('addDriver', [new Reference($id)]);
                }
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    public function setupFactory(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('dvdoug.code_coverage.driver.factory')) {
            return;
        }

        $factory = $container->getDefinition('dvdoug.code_coverage.driver.factory');
        $drivers = [];
        $ids = $container->findTaggedServiceIds('dvdoug.code_coverage.driver');

        foreach ($ids as $id => $attributes) {
            $drivers[] = $container->getDefinition($id)->getClass();
        }

        $factory->setArguments([$drivers]);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function setupCodeCoverage(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('behat.code_coverage.php_code_coverage')) {
            return;
        }

        $coverage = $container->getDefinition('behat.code_coverage.php_code_coverage');
        $config = $container->getParameter('behat.code_coverage.config.filter');

        $coverage->addMethodCall(
            'setAddUncoveredFilesFromWhitelist',
            [$config['whitelist']['addUncoveredFilesFromWhitelist']]
        );
        $coverage->addMethodCall(
            'setProcessUncoveredFilesFromWhiteList',
            [$config['whitelist']['processUncoveredFilesFromWhitelist']]
        );
        $coverage->addMethodCall(
            'setForceCoversAnnotation',
            [$config['forceCoversAnnotation']]
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function setupCodeCoverageFilter(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('behat.code_coverage.php_code_coverage_filter')) {
            return;
        }

        $filter = $container->getDefinition('behat.code_coverage.php_code_coverage_filter');
        $config = $container->getParameter('behat.code_coverage.config.filter');

        $dirs = [
            'addDirectoryToWhiteList' => ['whitelist', 'include', 'directories'],
            'removeDirectoryFromWhiteList' => ['whitelist', 'exclude', 'directories'],
        ];

        foreach ($dirs as $method => $hiera) {
            foreach ($config[$hiera[0]][$hiera[1]][$hiera[2]] as $path => $dir) {
                $filter->addMethodCall($method, [$path, $dir['suffix'], $dir['prefix']]);
            }
        }

        $files = [
            'addFileToWhiteList' => ['whitelist', 'include', 'files'],
            'removeFileFromWhiteList' => ['whitelist', 'exclude', 'files'],
        ];

        foreach ($files as $method => $hiera) {
            foreach ($config[$hiera[0]][$hiera[1]][$hiera[2]] as $file) {
                $filter->addMethodCall($method, [$file]);
            }
        }
    }
}

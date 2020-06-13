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
use DVDoug\Behat\CodeCoverage\Driver\RemoteXdebug;
use DVDoug\Behat\CodeCoverage\Listener\EventListener;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
use SebastianBergmann\Environment\Runtime;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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

        if (count($config['drivers'])) {
            $config['driver'] = $config['drivers'][0];
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
        $container->setParameter('behat.code_coverage.config.driver', $config['driver']);
        $container->setParameter('behat.code_coverage.config.drivers', $config['drivers']);
        $container->setParameter('behat.code_coverage.config.filter', $config['filter']);
        $container->setParameter('behat.code_coverage.config.report', $config['report'] ?? []);
        $container->setParameter('behat.code_coverage.config.reports', $config['reports'] ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
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
                    ->setDeprecated('The "drivers" option is deprecated. Use "driver" instead.')
                    ->prototype('scalar')->end()
                ->end()
                ->enumNode('driver')
                    ->values(['local', 'remote'])
                    ->defaultValue('local')
                ->end()
                ->arrayNode('filter')
                    ->addDefaultsIfNotSet()
                    ->children()
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
                    ->setDeprecated('The "report" option is deprecated. Use "reports" instead.')
                    ->children()
                        ->scalarNode('format')->defaultValue('html')->end()
                        ->arrayNode('options')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('reports')
                    ->children()
                        ->arrayNode('clover')
                            ->children()
                                ->scalarNode('name')->end()
                                ->scalarNode('target')->end()
                            ->end()
                        ->end()
                        ->arrayNode('crap4j')
                            ->children()
                                ->scalarNode('name')->end()
                                ->scalarNode('target')->end()
                            ->end()
                        ->end()
                        ->arrayNode('html')
                            ->children()
                                ->scalarNode('target')->end()
                                ->scalarNode('lowUpperBound')->end()
                                ->scalarNode('highLowerBound')->end()
                            ->end()
                        ->end()
                        ->arrayNode('php')
                            ->children()
                                ->scalarNode('target')->end()
                            ->end()
                        ->end()
                        ->arrayNode('text')
                            ->children()
                                ->booleanNode('showColors')->end()
                                ->scalarNode('lowUpperBound')->end()
                                ->scalarNode('highLowerBound')->end()
                                ->booleanNode('showOnlySummary')->end()
                                ->booleanNode('showUncoveredFiles')->end()
                            ->end()
                        ->end()
                        ->arrayNode('xml')
                            ->children()
                                ->scalarNode('target')->end()
                            ->end()
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
        /** @var InputInterface $input */
        $input = $container->get('cli.input');

        /** @var OutputInterface $output */
        $output = $container->get('cli.output');

        $runtime = new Runtime();
        $canCollectCodeCoverage = $runtime->canCollectCodeCoverage();

        if (!$canCollectCodeCoverage) {
            $output->writeln('<comment>No code coverage driver is available</comment>');
        }

        if (!$canCollectCodeCoverage || $input->hasParameterOption('--no-coverage')) {
            $container->getDefinition(EventListener::class)->setArgument('$coverage', null);
        }

        $this->setupCodeCoverageFilter($container);
        $this->setupCodeCoverage($container);
    }

    private function setupCodeCoverage(ContainerBuilder $container): void
    {
        $codeCoverage = $container->getDefinition(CodeCoverage::class);
        $filter = $container->getDefinition(Filter::class);
        $driverChoice = $container->getParameter('behat.code_coverage.config.driver');

        if ($driverChoice === 'remote') {
            $remoteXdebug = $container->getDefinition(RemoteXdebug::class);
            $codeCoverage->setArguments([$remoteXdebug, $filter]);
        } else {
            $codeCoverage->setFactory([self::class, 'initCodeCoverage']);
            $codeCoverage->setArguments([$filter]);
        }

        $config = $container->getParameter('behat.code_coverage.config.filter');

        $codeCoverage->addMethodCall(
            'includeUncoveredFiles',
            [$config['whitelist']['addUncoveredFilesFromWhitelist']]
        );
        $codeCoverage->addMethodCall(
            'processUncoveredFiles',
            [$config['whitelist']['processUncoveredFilesFromWhitelist']]
        );
    }

    private function setupCodeCoverageFilter(ContainerBuilder $container): void
    {
        $filter = $container->getDefinition(Filter::class);
        $config = $container->getParameter('behat.code_coverage.config.filter');

        $dirs = [
            'includeDirectory' => ['whitelist', 'include', 'directories'],
            'excludeDirectory' => ['whitelist', 'exclude', 'directories'],
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

    public static function initCodeCoverage(Filter $filter): CodeCoverage
    {
        try {
            $driver = Driver::forLineAndPathCoverage($filter);
        } catch (NoCodeCoverageDriverWithPathCoverageSupportAvailableException $e) {
            $driver = Driver::forLineCoverage($filter);
        }

        return new CodeCoverage($driver, $filter);
    }
}

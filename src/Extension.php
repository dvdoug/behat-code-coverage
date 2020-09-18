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
use DVDoug\Behat\CodeCoverage\Subscriber\EventSubscriber;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));

        $servicesFile = 'services.xml';
        $loader->load($servicesFile);

        $container->setParameter('behat.code_coverage.config.filter', $config['filter']);
        $container->setParameter('behat.code_coverage.config.branchAndPathCoverage', $config['branchAndPathCoverage']);
        $container->setParameter('behat.code_coverage.config.reports', $config['reports'] ?? []);
        $container->setParameter('behat.code_coverage.config.cache', $config['cache']);
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                ->scalarNode('cache')
                    ->defaultValue(sys_get_temp_dir() . '/behat-code-coverage-cache')
                ->end()
                ->booleanNode('branchAndPathCoverage')
                  ->defaultNull() // use null to mean auto
                ->end()
                ->arrayNode('filter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('includeUncoveredFiles')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('processUncoveredFiles')
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
                ->arrayNode('reports')
                    ->children()
                        ->arrayNode('clover')
                            ->children()
                                ->scalarNode('name')->defaultNull()->end()
                                ->scalarNode('target')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->arrayNode('crap4j')
                            ->children()
                                ->scalarNode('name')->defaultNull()->end()
                                ->scalarNode('target')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->arrayNode('html')
                            ->children()
                                ->scalarNode('target')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('lowUpperBound')->defaultValue(50)->end()
                                ->scalarNode('highLowerBound')->defaultValue(90)->end()
                            ->end()
                        ->end()
                        ->arrayNode('php')
                            ->children()
                                ->scalarNode('target')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->arrayNode('text')
                            ->children()
                                ->booleanNode('showColors')->defaultValue(false)->end()
                                ->scalarNode('lowUpperBound')->defaultValue(50)->end()
                                ->scalarNode('highLowerBound')->defaultValue(90)->end()
                                ->booleanNode('showOnlySummary')->defaultValue(false)->end()
                                ->booleanNode('showUncoveredFiles')->defaultValue(false)->end()
                            ->end()
                        ->end()
                        ->arrayNode('xml')
                            ->children()
                                ->scalarNode('target')->isRequired()->cannotBeEmpty()->end()
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

        $filterConfig = $container->getParameter('behat.code_coverage.config.filter');
        $branchPathConfig = $container->getParameter('behat.code_coverage.config.branchAndPathCoverage');
        $cacheDir = $container->getParameter('behat.code_coverage.config.cache');

        $canCollectCodeCoverage = true;
        try {
            $this->initCodeCoverage(new Filter(), $filterConfig, null, $cacheDir, $output);

            $codeCoverageDefinition = $container->getDefinition(CodeCoverage::class);
            $filterDefinition = $container->getDefinition(Filter::class);
            $codeCoverageDefinition->setFactory([new Reference(self::class), 'initCodeCoverage']);
            $codeCoverageDefinition->setArguments([$filterDefinition, $filterConfig, $branchPathConfig, $cacheDir, $output]);
        } catch (NoCodeCoverageDriverAvailableException $e) {
            $output->writeln('<comment>No code coverage driver is available</comment>');
            $canCollectCodeCoverage = false;
        }

        if (!$canCollectCodeCoverage || $input->hasParameterOption('--no-coverage')) {
            $container->getDefinition(EventSubscriber::class)->setArgument('$coverage', null);
        }
    }

    public function initCodeCoverage(Filter $filter, array $filterConfig, ?bool $branchPathConfig, string $cacheDir, OutputInterface $output): CodeCoverage
    {
        // set up filter
        array_walk($filterConfig['include']['directories'], static function (array $dir, string $path, Filter $filter): void {
            $filter->includeDirectory($path, $dir['suffix'], $dir['prefix']);
        }, $filter);

        array_walk($filterConfig['include']['files'], static function (string $file, string $key, Filter $filter): void {
            $filter->includeFile($file);
        }, $filter);

        array_walk($filterConfig['exclude']['directories'], static function (array $dir, string $path, Filter $filter): void {
            $filter->excludeDirectory($path, $dir['suffix'], $dir['prefix']);
        }, $filter);

        array_walk($filterConfig['exclude']['files'], static function (string $file, string $key, Filter $filter): void {
            $filter->excludeFile($file);
        }, $filter);

        // see if we can get a driver
        $selector = new Selector();
        $driver = $selector->forLineCoverage($filter);
        if ($branchPathConfig !== false) {
            try {
                $driver = $selector->forLineAndPathCoverage($filter);
            } catch (NoCodeCoverageDriverWithPathCoverageSupportAvailableException $e) {
                // fallback driver is already set
                if ($branchPathConfig === true) { //only warn if explicitly enabled
                    $output->writeln(sprintf('<info>%s does not support collecting branch and path data</info>', $driver->nameAndVersion()));
                }
            }
        }

        // and init coverage
        $codeCoverage = new CodeCoverage($driver, $filter);
        $codeCoverage->cacheStaticAnalysis($cacheDir);

        if ($filterConfig['includeUncoveredFiles']) {
            $codeCoverage->includeUncoveredFiles();
        } else {
            $codeCoverage->excludeUncoveredFiles();
        }

        if ($filterConfig['processUncoveredFiles']) {
            $codeCoverage->processUncoveredFiles();
        } else {
            $codeCoverage->doNotProcessUncoveredFiles();
        }

        return $codeCoverage;
    }
}

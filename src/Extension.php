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

        $container->setParameter('behat.code_coverage.config.filter', $config['filter']);
        $container->setParameter('behat.code_coverage.config.reports', $config['reports'] ?? []);
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->children()
                ->arrayNode('filter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('addUncoveredFiles')
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
                                ->scalarNode('name')->end()
                                ->scalarNode('target')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->arrayNode('crap4j')
                            ->children()
                                ->scalarNode('name')->end()
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

        $codeCoverage->setFactory([self::class, 'initCodeCoverage']);
        $codeCoverage->setArguments([$filter]);

        $config = $container->getParameter('behat.code_coverage.config.filter');

        $codeCoverage->addMethodCall(
            'includeUncoveredFiles',
            [$config['addUncoveredFiles']]
        );
        $codeCoverage->addMethodCall(
            'processUncoveredFiles',
            [$config['processUncoveredFiles']]
        );
    }

    private function setupCodeCoverageFilter(ContainerBuilder $container): void
    {
        $filter = $container->getDefinition(Filter::class);
        $config = $container->getParameter('behat.code_coverage.config.filter');

        foreach ($config['include']['directories'] as $path => $dir) {
            $filter->addMethodCall('includeDirectory', [$path, $dir['suffix'], $dir['prefix']]);
        }

        foreach ($config['include']['files'] as $file) {
            $filter->addMethodCall('includeFile', [$file]);
        }

        foreach ($config['exclude']['directories'] as $path => $dir) {
            $filter->addMethodCall('excludeDirectory', [$path, $dir['suffix'], $dir['prefix']]);
        }

        foreach ($config['exclude']['files'] as $file) {
            $filter->addMethodCall('excludeFile', [$file]);
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

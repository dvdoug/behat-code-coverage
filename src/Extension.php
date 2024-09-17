<?php
/**
 * Behat Code Coverage
 */
declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage;

use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use DVDoug\Behat\CodeCoverage\Subscriber\EventSubscriber;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Driver\XdebugNotAvailableException;
use SebastianBergmann\CodeCoverage\Driver\XdebugNotEnabledException;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
use SebastianBergmann\FileIterator\Facade as FileIteratorFacade;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

use function sprintf;
use function sys_get_temp_dir;
use function realpath;

class Extension implements ExtensionInterface
{
    public function initialize(ExtensionManager $extensionManager): void
    {
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $container->registerForAutoconfiguration(Controller::class)->addTag(CliExtension::CONTROLLER_TAG);
        $container->registerForAutoconfiguration(EventSubscriber::class)->addTag(EventDispatcherExtension::SUBSCRIBER_TAG);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.php');

        $container->setParameter('behat.code_coverage.config.filter', $config['filter']);
        $container->setParameter('behat.code_coverage.config.branchAndPathCoverage', $config['branchAndPathCoverage']);
        $container->setParameter('behat.code_coverage.config.reports', $config['reports'] ?? []);
        $container->setParameter('behat.code_coverage.config.cache', $config['cache']);
    }

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
                            ->setDeprecated('dvdoug/behat-code-coverage', '5.3', 'the processUncoveredFiles setting is deprecated, it has been removed from php-code-coverage v10')
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
                        ->arrayNode('cobertura')
                            ->children()
                                ->scalarNode('name')->defaultNull()->end()
                                ->scalarNode('target')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                        ->end()
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
                                ->arrayNode('colors')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('successLow')->defaultValue('#dff0d8')->end()
                                        ->scalarNode('successMedium')->defaultValue('#c3e3b5')->end()
                                        ->scalarNode('successHigh')->defaultValue('#99cb84')->end()
                                        ->scalarNode('warning')->defaultValue('#fcf8e3')->end()
                                        ->scalarNode('danger')->defaultValue('#f2dede')->end()
                                    ->end()
                                ->end()
                                ->scalarNode('customCSSFile')->defaultNull()->end()
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

    public function getConfigKey()
    {
        return 'code_coverage';
    }

    public function process(ContainerBuilder $container): void
    {
        /** @var InputInterface $input */
        $input = $container->get(CliExtension::INPUT_ID);

        /** @var OutputInterface $output */
        $output = $container->get(CliExtension::OUTPUT_ID);

        if ($output instanceof ConsoleOutputInterface) {
            $output = $output->getErrorOutput();
        }

        $filterConfig = $container->getParameter('behat.code_coverage.config.filter');
        $branchPathConfig = $container->getParameter('behat.code_coverage.config.branchAndPathCoverage');
        $cacheDir = $container->getParameter('behat.code_coverage.config.cache');

        $canCollectCodeCoverage = true;
        try {
            $this->initCodeCoverage(new Filter(), $filterConfig, $branchPathConfig, $cacheDir, $output);

            $codeCoverageDefinition = $container->getDefinition(CodeCoverage::class);
            $filterDefinition = $container->getDefinition(Filter::class);
            $codeCoverageDefinition->setFactory([new Reference(self::class), 'initCodeCoverage']);
            $codeCoverageDefinition->setArguments([$filterDefinition, $filterConfig, $branchPathConfig, $cacheDir, $output]);
        } catch (NoCodeCoverageDriverAvailableException|XdebugNotEnabledException|XdebugNotAvailableException $e) {
            $output->writeln("<comment>No code coverage driver is available. {$e->getMessage()}</comment>");
            $canCollectCodeCoverage = false;
        }

        if (!$canCollectCodeCoverage || $input->hasParameterOption('--no-coverage')) {
            $container->getDefinition(EventSubscriber::class)->setArgument('$coverage', null);
        }
    }

    public function initCodeCoverage(Filter $filter, array $filterConfig, ?bool $branchPathConfig, string $cacheDir, OutputInterface $output): CodeCoverage
    {
        // set up filter
        $files = [];

        foreach ($filterConfig['include']['directories'] as $directoryToInclude => $details) {
            foreach ((new FileIteratorFacade())->getFilesAsArray($directoryToInclude, $details['suffix'], $details['prefix']) as $fileToInclude) {
                $files[realpath($fileToInclude)] = realpath($fileToInclude);
            }
        }

        foreach ($filterConfig['include']['files'] as $fileToInclude) {
            $files[$fileToInclude] = $fileToInclude;
        }

        foreach ($filterConfig['exclude']['directories'] as $directoryToExclude => $details) {
            foreach ((new FileIteratorFacade())->getFilesAsArray($directoryToExclude, $details['suffix'], $details['prefix']) as $fileToExclude) {
                unset($files[$fileToExclude]);
            }
        }

        foreach ($filterConfig['exclude']['files'] as $fileToExclude) {
            unset($files[realpath($fileToExclude)]);
        }

        foreach ($files as $file) {
            $filter->includeFile($file);
        }

        // see if we can get a driver
        $selector = new Selector();
        $driver = $selector->forLineCoverage($filter);
        if ($branchPathConfig !== false) {
            try {
                $driver = $selector->forLineAndPathCoverage($filter);
            } catch (NoCodeCoverageDriverWithPathCoverageSupportAvailableException|XdebugNotAvailableException|XdebugNotEnabledException $e) {
                // fallback driver is already set
                if ($branchPathConfig === true) { // only warn if explicitly enabled
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

        return $codeCoverage;
    }
}

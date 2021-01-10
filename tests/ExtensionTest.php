<?php

declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Test;

use Behat\Testwork\ServiceContainer\Configuration\ConfigurationTree;
use Behat\Testwork\ServiceContainer\ContainerLoader;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use function dirname;
use DVDoug\Behat\CodeCoverage\Extension;
use DVDoug\Behat\CodeCoverage\Service\ReportService;
use DVDoug\Behat\CodeCoverage\Subscriber\EventSubscriber;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use SebastianBergmann\Environment\Runtime;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use function sys_get_temp_dir;

class ExtensionTest extends TestCase
{
    public function testConfigParses(): void
    {
        $tree = new ConfigurationTree();
        $config = $tree->getConfigTree(
            [
                'code_coverage' => new Extension(),
            ]
        );

        self::assertInstanceOf(NodeInterface::class, $config);
    }

    public function testContainerLoads(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('paths.base', dirname(__DIR__));
        $container->setParameter('extensions', [Extension::class]);

        $loader = new ContainerLoader(new ExtensionManager([]));
        $loader->load(
            $container,
            [
                [
                    'extensions' => [
                        Extension::class => [],
                    ],
                ],
            ]
        );

        self::assertTrue($container->hasParameter('behat.code_coverage.config.filter'));
        self::assertTrue($container->hasParameter('behat.code_coverage.config.reports'));
        self::assertTrue($container->hasParameter('behat.code_coverage.config.branchAndPathCoverage'));
    }

    public function testContainerBuildsIncludingCoveredFiles(): void
    {
        if (!(new Runtime())->canCollectCodeCoverage()) {
            $this->markTestSkipped('Requires code coverage enabled');
        }

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $container = new ContainerBuilder();

        $container->set('cli.input', $input);
        $container->set('cli.output', $output);
        $container->setDefinition(Extension::class, new Definition(Extension::class));
        $container->setDefinition(EventSubscriber::class, new Definition(EventSubscriber::class));
        $container->getDefinition(EventSubscriber::class)->setArgument(0, ReportService::class);
        $container->setDefinition(Filter::class, new Definition(Filter::class));
        $container->setDefinition(CodeCoverage::class, new Definition(CodeCoverage::class));

        $container->setParameter('behat.code_coverage.config.branchAndPathCoverage', false);
        $container->setParameter('behat.code_coverage.config.cache', sys_get_temp_dir());
        $container->setParameter(
            'behat.code_coverage.config.filter',
            [
                'include' => [
                    'directories' => [
                        '/tmp/foo' => ['suffix' => '.php', 'prefix' => ''],
                    ],
                    'files' => [
                        '/tmp/foo',
                    ],
                ],
                'exclude' => [
                    'directories' => [
                        '/tmp/foo' => ['suffix' => '.php', 'prefix' => ''],
                    ],
                    'files' => [
                        '/tmp/foo',
                    ],
                ],
                'includeUncoveredFiles' => true,
                'processUncoveredFiles' => true,
            ]
        );

        $extension = new Extension();
        $extension->process($container);

        $coverage = $container->get(CodeCoverage::class);

        self::assertInstanceOf(CodeCoverage::class, $coverage);
    }

    public function testContainerBuildsExcludingCoveredFiles(): void
    {
        if (!(new Runtime())->canCollectCodeCoverage()) {
            $this->markTestSkipped('Requires code coverage enabled');
        }

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $container = new ContainerBuilder();

        $container->set('cli.input', $input);
        $container->set('cli.output', $output);
        $container->setDefinition(Extension::class, new Definition(Extension::class));
        $container->setDefinition(EventSubscriber::class, new Definition(EventSubscriber::class));
        $container->getDefinition(EventSubscriber::class)->setArgument(0, ReportService::class);
        $container->setDefinition(Filter::class, new Definition(Filter::class));
        $container->setDefinition(CodeCoverage::class, new Definition(CodeCoverage::class));

        $container->setParameter('behat.code_coverage.config.branchAndPathCoverage', true);
        $container->setParameter('behat.code_coverage.config.cache', sys_get_temp_dir());
        $container->setParameter(
            'behat.code_coverage.config.filter',
            [
                'include' => [
                    'directories' => [
                        '/tmp/foo' => ['suffix' => '.php', 'prefix' => ''],
                    ],
                    'files' => [
                        '/tmp/foo',
                    ],
                ],
                'exclude' => [
                    'directories' => [
                        '/tmp/foo' => ['suffix' => '.php', 'prefix' => ''],
                    ],
                    'files' => [
                        '/tmp/foo',
                    ],
                ],
                'includeUncoveredFiles' => false,
                'processUncoveredFiles' => false,
            ]
        );

        $extension = new Extension();
        $extension->process($container);

        $coverage = $container->get(CodeCoverage::class);
        self::assertInstanceOf(CodeCoverage::class, $coverage);
    }

    public function testContainerBuildsWithCoverageSkipped(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('hasParameterOption')->willReturn('--no-coverage');
        $output = $this->createMock(OutputInterface::class);

        $container = new ContainerBuilder();

        $container->set('cli.input', $input);
        $container->set('cli.output', $output);
        $container->setDefinition(Extension::class, new Definition(Extension::class));
        $container->setDefinition(Filter::class, new Definition(Filter::class));
        $container->setDefinition(CodeCoverage::class, new Definition(CodeCoverage::class));

        $eventSubscriber = new Definition(EventSubscriber::class);
        $eventSubscriber->setArgument(0, ReportService::class);
        $eventSubscriber->setPublic(true);
        $container->setDefinition(EventSubscriber::class, $eventSubscriber);

        $container->setParameter('behat.code_coverage.config.branchAndPathCoverage', false);
        $container->setParameter('behat.code_coverage.config.cache', sys_get_temp_dir());
        $container->setParameter(
            'behat.code_coverage.config.filter',
            [
                'include' => [
                    'directories' => [
                        '/tmp/foo' => ['suffix' => '.php', 'prefix' => ''],
                    ],
                    'files' => [
                        '/tmp/foo',
                    ],
                ],
                'exclude' => [
                    'directories' => [
                        '/tmp/foo' => ['suffix' => '.php', 'prefix' => ''],
                    ],
                    'files' => [
                        '/tmp/foo',
                    ],
                ],
                'includeUncoveredFiles' => false,
                'processUncoveredFiles' => false,
            ]
        );

        $extension = new Extension();
        $extension->process($container);

        $container->compile();

        self::assertNull($container->getDefinition(EventSubscriber::class)->getArgument(1));
    }

    public function testContainerBuildsWithCoverageUnavailable(): void
    {
        $driverClassReflection = new ReflectionClass(Driver::class);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $container = new ContainerBuilder();

        $container->set('cli.input', $input);
        $container->set('cli.output', $output);
        $container->setDefinition(Filter::class, new Definition(Filter::class));
        $container->setDefinition(CodeCoverage::class, new Definition(CodeCoverage::class));

        $eventSubscriber = new Definition(EventSubscriber::class);
        $eventSubscriber->setArgument(0, ReportService::class);
        $eventSubscriber->setPublic(true);
        $container->setDefinition(EventSubscriber::class, $eventSubscriber);

        $container->setParameter('behat.code_coverage.config.branchAndPathCoverage', false);
        $container->setParameter('behat.code_coverage.config.cache', sys_get_temp_dir());
        $container->setParameter(
            'behat.code_coverage.config.filter',
            [
                'include' => [
                    'directories' => [
                        '/tmp/foo' => ['suffix' => '.php', 'prefix' => ''],
                    ],
                    'files' => [
                        '/tmp/foo',
                    ],
                ],
                'exclude' => [
                    'directories' => [
                        '/tmp/foo' => ['suffix' => '.php', 'prefix' => ''],
                    ],
                    'files' => [
                        '/tmp/foo',
                    ],
                ],
                'includeUncoveredFiles' => false,
                'processUncoveredFiles' => false,
            ]
        );

        $extension = $this->createPartialMock(Extension::class, ['initCodeCoverage']);
        if ($driverClassReflection->isInterface()) {
            $extension->method('initCodeCoverage')->willThrowException(new RuntimeException());
        } else {
            $extension->method('initCodeCoverage')->willThrowException(new NoCodeCoverageDriverAvailableException());
        }

        $extension->process($container);

        $container->compile();

        self::assertNull($container->getDefinition(EventSubscriber::class)->getArgument(1));
    }
}

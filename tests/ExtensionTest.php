<?php

declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Test;

use Behat\Testwork\ServiceContainer\Configuration\ConfigurationTree;
use Behat\Testwork\ServiceContainer\ContainerLoader;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use DVDoug\Behat\CodeCoverage\Extension;
use DVDoug\Behat\CodeCoverage\Service\ReportService;
use DVDoug\Behat\CodeCoverage\Subscriber\EventSubscriber;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

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
    }

    public function testContainerBuilds1(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $container = new ContainerBuilder();

        $container->set('cli.input', $input);
        $container->set('cli.output', $output);
        $container->setDefinition(EventSubscriber::class, new Definition(EventSubscriber::class));
        $container->getDefinition(EventSubscriber::class)->setArgument(0, ReportService::class);
        $container->setDefinition(Filter::class, new Definition(Filter::class));
        $container->setDefinition(CodeCoverage::class, new Definition(CodeCoverage::class));

        $container->setParameter(
            'behat.code_coverage.config.filter',
            [
                'include' => [
                    'directories' => [
                        '/tmp' => ['suffix' => '.php', 'prefix' => ''],
                    ],
                    'files' => [
                        '/tmp/foo',
                    ],
                ],
                'exclude' => [
                    'directories' => [
                        '/tmp' => ['suffix' => '.php', 'prefix' => ''],
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

        $container->compile();

        self::assertInstanceOf(Container::class, $container);
    }

    public function testContainerBuilds2(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $container = new ContainerBuilder();

        $container->set('cli.input', $input);
        $container->set('cli.output', $output);
        $container->setDefinition(EventSubscriber::class, new Definition(EventSubscriber::class));
        $container->getDefinition(EventSubscriber::class)->setArgument(0, ReportService::class);
        $container->setDefinition(Filter::class, new Definition(Filter::class));
        $container->setDefinition(CodeCoverage::class, new Definition(CodeCoverage::class));

        $container->setParameter(
            'behat.code_coverage.config.filter',
            [
                'include' => [
                    'directories' => [
                        '/tmp' => ['suffix' => '.php', 'prefix' => ''],
                    ],
                    'files' => [
                        '/tmp/foo',
                    ],
                ],
                'exclude' => [
                    'directories' => [
                        '/tmp' => ['suffix' => '.php', 'prefix' => ''],
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

        self::assertInstanceOf(Container::class, $container);
    }
}

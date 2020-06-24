<?php

declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Test;

use Behat\Testwork\ServiceContainer\Configuration\ConfigurationTree;
use DVDoug\Behat\CodeCoverage\Extension;
use DVDoug\Behat\CodeCoverage\Listener\EventListener;
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

    public function testContainerBuilds(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $container = new ContainerBuilder();

        $container->set('cli.input', $input);
        $container->set('cli.output', $output);
        $container->setDefinition(EventListener::class, new Definition(EventListener::class));
        $container->setDefinition(Filter::class, new Definition(Filter::class));
        $container->setDefinition(CodeCoverage::class, new Definition(CodeCoverage::class));

        $container->setParameter(
            'behat.code_coverage.config.filter',
            [
                'include' => [
                    'directories' => [],
                    'files' => [],
                ],
                'exclude' => [
                    'directories' => [],
                    'files' => [],
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
}

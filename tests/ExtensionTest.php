<?php

declare(strict_types=1);
/**
 * Extension.
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace DVDoug\Behat\CodeCoverage;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use org\bovigo\vfs\vfsStream;

/**
 * Extension test.
 *
 * @group Unit
 */
class ExtensionTest extends TestCase
{
    /**
     * @dataProvider loadProvider
     */
    public function testLoad($expected, $config): void
    {
        $vfsRoot = vfsStream::setup('configDir');
        $configDir = vfsStream::url('configDir');
        $servicesFile = 'services.xml';

        file_put_contents(
            $configDir . '/' . $servicesFile,
            <<<END_OF_CONFIG
<?xml version="1.0" ?>
<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <parameters>
        <parameter key="behat.code_coverage.service.report.class">DVDoug\Behat\CodeCoverage\Service\ReportService</parameter>
    </parameters>

    <services>
         <service id="behat.code_coverage.service.report" class="%behat.code_coverage.service.report.class%" />
    </services>
</container>
END_OF_CONFIG
        );

        $container = new ContainerBuilder();

        $extension = new Extension($configDir);
        $extension->load($container, $config);

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $container->getParameter($key));
        }
    }

    /**
     * @return array
     */
    public function loadProvider()
    {
        return [
            [
                [
                    'behat.code_coverage.config.auth' => [
                        'user' => 'test_user',
                        'password' => 'test_password',
                    ],
                    'behat.code_coverage.config.create' => [
                        'method' => 'CREATE',
                        'path' => 'create_path',
                    ],
                    'behat.code_coverage.config.read' => [
                        'method' => 'READ',
                        'path' => 'read_path',
                    ],
                    'behat.code_coverage.config.delete' => [
                        'method' => 'DELETE',
                        'path' => 'delete_path',
                    ],
                    'behat.code_coverage.config.drivers' => ['remote'],
                    'behat.code_coverage.config.filter' => [
                        'whitelist' => [
                            'addUncoveredFilesFromWhitelist' => false,
                            'processUncoveredFilesFromWhitelist' => true,
                            'include' => [
                                'directories' => [
                                    'directory1' => [
                                        'prefix' => 'Secure',
                                        'suffix' => '.php',
                                    ],
                                ],
                                'files' => [
                                    'file1',
                                ],
                            ],
                            'exclude' => [
                                'directories' => [
                                    'directory2' => [
                                        'prefix' => 'Insecure',
                                        'suffix' => '.inc',
                                    ],
                                ],
                                'files' => [
                                    'file2',
                                ],
                            ],
                        ],
                        'blacklist' => [
                            'include' => [
                                'directories' => [
                                    'directory3' => [
                                        'prefix' => 'Public',
                                        'suffix' => '.php',
                                    ],
                                ],
                                'files' => [
                                    'file3',
                                ],
                            ],
                            'exclude' => [
                                'directories' => [
                                    'directory4' => [
                                        'prefix' => 'Private',
                                        'suffix' => '.inc',
                                    ],
                                ],
                                'files' => [
                                    'file4',
                                ],
                            ],
                        ],
                        'forceCoversAnnotation' => true,
                        'mapTestClassNameToCoveredClassName' => true,
                    ],
                    'behat.code_coverage.config.report' => [
                        'format' => 'fmt',
                        'options' => [
                            'target' => '/tmp',
                        ],
                    ],
                ],
                [
                    'auth' => [
                        'user' => 'test_user',
                        'password' => 'test_password',
                    ],
                    'create' => [
                        'method' => 'CREATE',
                        'path' => 'create_path',
                    ],
                    'read' => [
                        'method' => 'READ',
                        'path' => 'read_path',
                    ],
                    'delete' => [
                        'method' => 'DELETE',
                        'path' => 'delete_path',
                    ],
                    'drivers' => ['remote'],
                    'filter' => [
                        'whitelist' => [
                            'addUncoveredFilesFromWhitelist' => false,
                            'processUncoveredFilesFromWhitelist' => true,
                            'include' => [
                                'directories' => [
                                    'directory1' => [
                                        'prefix' => 'Secure',
                                        'suffix' => '.php',
                                    ],
                                ],
                                'files' => [
                                    'file1',
                                ],
                            ],
                            'exclude' => [
                                'directories' => [
                                    'directory2' => [
                                        'prefix' => 'Insecure',
                                        'suffix' => '.inc',
                                    ],
                                ],
                                'files' => [
                                    'file2',
                                ],
                            ],
                        ],
                        'blacklist' => [
                            'include' => [
                                'directories' => [
                                    'directory3' => [
                                        'prefix' => 'Public',
                                        'suffix' => '.php',
                                    ],
                                ],
                                'files' => [
                                    'file3',
                                ],
                            ],
                            'exclude' => [
                                'directories' => [
                                    'directory4' => [
                                        'prefix' => 'Private',
                                        'suffix' => '.inc',
                                    ],
                                ],
                                'files' => [
                                    'file4',
                                ],
                            ],
                        ],
                        'forceCoversAnnotation' => true,
                        'mapTestClassNameToCoveredClassName' => true,
                    ],
                    'report' => [
                        'format' => 'fmt',
                        'options' => [
                            'target' => '/tmp',
                        ],
                    ],
                ],
            ],
            [
                [
                    'behat.code_coverage.config.auth' => null,
                    'behat.code_coverage.config.create' => [
                        'method' => 'POST',
                        'path' => '/',
                    ],
                    'behat.code_coverage.config.read' => [
                        'method' => 'GET',
                        'path' => '/',
                    ],
                    'behat.code_coverage.config.delete' => [
                        'method' => 'DELETE',
                        'path' => '/',
                    ],
                    'behat.code_coverage.config.drivers' => ['local'],
                    'behat.code_coverage.config.filter' => [
                        'whitelist' => [
                            'addUncoveredFilesFromWhitelist' => true,
                            'processUncoveredFilesFromWhitelist' => false,
                            'include' => [
                                'directories' => [],
                                'files' => [],
                            ],
                            'exclude' => [
                                'directories' => [],
                                'files' => [],
                            ],
                        ],
                        'blacklist' => [
                            'include' => [
                                'directories' => [],
                                'files' => [],
                            ],
                            'exclude' => [
                                'directories' => [],
                                'files' => [],
                            ],
                        ],
                        'forceCoversAnnotation' => false,
                        'mapTestClassNameToCoveredClassName' => false,
                    ],
                ],
                [
                    'create' => [
                        'method' => 'POST',
                        'path' => '/',
                    ],
                    'read' => [
                        'method' => 'GET',
                        'path' => '/',
                    ],
                    'delete' => [
                        'method' => 'DELETE',
                        'path' => '/',
                    ],
                    'drivers' => [],
                    'filter' => [
                        'whitelist' => [
                            'addUncoveredFilesFromWhitelist' => true,
                            'processUncoveredFilesFromWhitelist' => false,
                            'include' => [
                                'directories' => [],
                                'files' => [],
                            ],
                            'exclude' => [
                                'directories' => [],
                                'files' => [],
                            ],
                        ],
                        'blacklist' => [
                            'include' => [
                                'directories' => [],
                                'files' => [],
                            ],
                            'exclude' => [
                                'directories' => [],
                                'files' => [],
                            ],
                        ],
                        'forceCoversAnnotation' => false,
                        'mapTestClassNameToCoveredClassName' => false,
                    ],
                    'report' => [
                        'format' => 'html',
                        'options' => [],
                    ],
                ],
            ],
        ];
    }

    public function testConfigure(): void
    {
        $builder = new ArrayNodeDefinition('test');

        $extension = new Extension();
        $extension->configure($builder);

        $children = $this->getPropertyOnObject($builder, 'children');

        $this->assertCount(7, $children);
        $this->assertTrue(isset($children['auth']));
        $this->assertTrue(isset($children['create']));
        $this->assertTrue(isset($children['read']));
        $this->assertTrue(isset($children['delete']));
        $this->assertTrue(isset($children['drivers']));
        $this->assertTrue(isset($children['filter']));
        $this->assertTrue(isset($children['report']));

        $auth = $this->getPropertyOnObject($children['auth'], 'children');

        $this->assertCount(2, $auth);
        $this->assertTrue(isset($auth['user']));
        $this->assertTrue(isset($auth['password']));

        $create = $this->getPropertyOnObject($children['create'], 'children');

        $this->assertCount(2, $create);
        $this->assertTrue(isset($create['method']));
        $this->assertTrue(isset($create['path']));

        $read = $this->getPropertyOnObject($children['read'], 'children');

        $this->assertCount(2, $read);
        $this->assertTrue(isset($read['method']));
        $this->assertTrue(isset($read['path']));

        $delete = $this->getPropertyOnObject($children['delete'], 'children');

        $this->assertCount(2, $delete);
        $this->assertTrue(isset($delete['method']));
        $this->assertTrue(isset($delete['path']));

        $report = $this->getPropertyOnObject($children['report'], 'children');

        $this->assertCount(2, $report);
        $this->assertTrue(isset($report['format']));
        $this->assertTrue(isset($report['options']));
    }

    public function testProcess(): void
    {
        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $input = $this->createMock('Symfony\Component\Console\Input\ArgvInput');

        $input->expects($this->exactly(1))
            ->method('hasParameterOption');

        $container->expects($this->exactly(4))
                  ->method('hasDefinition');
        $container->expects($this->exactly(1))
                  ->method('get')->willReturn($input);

        $extension = new Extension();

        $compilerPasses = $extension->process($container);
    }

    /**
     * Gets the given property of an object.
     *
     * @param mixed  $object Object
     * @param string $name   Property name
     *
     * @return mixed
     */
    private function getPropertyOnObject($object, $name)
    {
        $property = new \ReflectionProperty($object, $name);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}

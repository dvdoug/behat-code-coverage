<?php
/**
 * Extension
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VIPSoft\TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Extension test
 *
 * @group Unit
 */
class ExtensionTest extends TestCase
{
    /**
     * @dataProvider loadProvider
     */
    public function testLoad($expected, $config)
    {
        $vfsRoot = vfsStream::setup('configDir');
        $configDir = vfsStream::url('configDir');
        file_put_contents($configDir . '/services.xml', <<<END_OF_CONFIG
<?xml version="1.0" ?>
<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <parameters>
        <parameter key="behat.code_coverage.service.report.class">VIPSoft\CodeCoverageExtension\Service\ReportService</parameter>
    </parameters>

    <services>
         <service id="behat.code_coverage.service.report" class="%behat.code_coverage.service.report.class%" />
    </services>
</container>
END_OF_CONFIG
        );

        $container = new ContainerBuilder;

        $extension = new Extension($configDir);
        $extension->load($config, $container);

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $container->getParameter($key));
        }
    }

    /**
     * @return array
     */
    public function loadProvider()
    {
        return array(
            array(
                array(
                    'behat.code_coverage.config.auth' => array(
                        'user'     => 'test_user',
                        'password' => 'test_password',
                    ),
                    'behat.code_coverage.config.create' => array(
                        'method' => 'CREATE',
                        'path'   => 'create_path',
                    ),
                    'behat.code_coverage.config.read' => array(
                        'method' => 'READ',
                        'path'   => 'read_path',
                    ),
                    'behat.code_coverage.config.delete' => array(
                        'method' => 'DELETE',
                        'path'   => 'delete_path',
                    ),
                    'behat.code_coverage.config.drivers' => array('remote'),
                    'behat.code_coverage.config.filter' => null,
                    'behat.code_coverage.config.report' => array(
                        'format'    => 'fmt',
                        'options' => array(
                            'target' => '/tmp'
                        )
                    )
                ),
                array(
                    'auth' => array(
                        'user'     => 'test_user',
                        'password' => 'test_password',
                    ),
                    'create' => array(
                        'method' => 'CREATE',
                        'path'   => 'create_path',
                    ),
                    'read' => array(
                        'method' => 'READ',
                        'path'   => 'read_path',
                    ),
                    'delete' => array(
                        'method' => 'DELETE',
                        'path'   => 'delete_path',
                    ),
                    'drivers' => array('remote'),
                    'filter' => null,
                    'report' => array(
                        'format'    => 'fmt',
                        'options' => array(
                            'target' => '/tmp'
                        )
                    )
                ),
            ),
            array(
                array(
                    'behat.code_coverage.config.auth' => null,
                    'behat.code_coverage.config.create' => array(
                        'method' => 'POST',
                        'path'   => '/',
                    ),
                    'behat.code_coverage.config.read' => array(
                        'method' => 'GET',
                        'path'   => '/',
                    ),
                    'behat.code_coverage.config.delete' => array(
                        'method' => 'DELETE',
                        'path'   => '/',
                    ),
                    'behat.code_coverage.config.drivers' => array('remote', 'local'),
                ),
                array(),
            ),
        );
    }

    public function testGetConfig()
    {
        $builder = new ArrayNodeDefinition('test');

        $extension = new Extension();
        $extension->getConfig($builder);

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

    public function testGetCompilerPasses()
    {
        $extension = new Extension();

        $compilerPasses = $extension->getCompilerPasses();

        $this->assertTrue(is_array($compilerPasses));
        $this->assertCount(2, $compilerPasses);
    }

    /**
     * Gets the given property of an object
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

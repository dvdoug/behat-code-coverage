<?php
/**
 * Event Listener
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Listener;

use VIPSoft\TestCase;
use Behat\Behat\Event\SuiteEvent;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioNode;

/**
 * @group Unit
 */
class EventListenerTest extends TestCase
{
    private $container;
    private $service;
    private $config;

    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->service   = $this->getMockBuilder('VIPSoft\CodeCoverageExtension\Service\ReportService')
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->config = array(
            'driver' => array(
                'service' => 'code_coverage.driver'
            )
        );
    }

    public function testGetSubscribedEvents()
    {
        $listener = new EventListener($this->config, $this->container, $this->service);
        $events   = $listener->getSubscribedEvents();

        $this->assertTrue(is_array($events));
        $this->assertEquals(4, count($events));
        $this->assertEquals('beforeSuite', $events['beforeSuite']);
        $this->assertEquals('afterSuite', $events['afterSuite']);
        $this->assertEquals('beforeScenario', $events['beforeScenario']);
        $this->assertEquals('afterScenario', $events['afterScenario']);
    }

    public function testBeforeSuite()
    {
        $coverage = $this->getMockBuilder('PHP_CodeCoverage')
                         ->disableOriginalConstructor()
                         ->getMock();
        $coverage->expects($this->once())
                 ->method('clear');

        $this->container->expects($this->once())
                        ->method('get')
                        ->with('code_coverage.driver')
                        ->will($this->returnValue($coverage));

        $event = $this->getMockBuilder('Behat\Behat\Event\SuiteEvent')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->config, $this->container, $this->service);
        $listener->beforeSuite($event);
    }

    public function testAfterSuite()
    {
        $coverage = $this->getMockBuilder('PHP_CodeCoverage')
                         ->disableOriginalConstructor()
                         ->getMock();

        $this->container->expects($this->once())
                        ->method('get')
                        ->with('code_coverage.driver')
                        ->will($this->returnValue($coverage));

        $this->service->expects($this->once())
                      ->method('generateReport');

        $event = $this->getMockBuilder('Behat\Behat\Event\SuiteEvent')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->config, $this->container, $this->service);
        $listener->afterSuite($event);
    }

    public function testBeforeScenario()
    {
        $coverage = $this->getMockBuilder('PHP_CodeCoverage')
                         ->disableOriginalConstructor()
                         ->getMock();
        $coverage->expects($this->once())
                 ->method('start')
                 ->with('title:1');

        $this->container->expects($this->once())
                        ->method('get')
                        ->with('code_coverage.driver')
                        ->will($this->returnValue($coverage));

        $node = new ScenarioNode('title', 1);

        $event = $this->getMockBuilder('Behat\Behat\Event\ScenarioEvent')
                      ->disableOriginalConstructor()
                      ->getMock();
        $event->expects($this->once())
              ->method('getScenario')
              ->will($this->returnValue($node));

        $listener = new EventListener($this->config, $this->container, $this->service);
        $listener->beforeScenario($event);
    }

    public function testBeforeOutlineExample()
    {
        $coverage = $this->getMockBuilder('PHP_CodeCoverage')
                         ->disableOriginalConstructor()
                         ->getMock();
        $coverage->expects($this->once())
                 ->method('start')
                 ->with('title:1');

        $this->container->expects($this->once())
                        ->method('get')
                        ->with('code_coverage.driver')
                        ->will($this->returnValue($coverage));

        $node = new OutlineNode('title', 1);

        $event = $this->getMockBuilder('Behat\Behat\Event\OutlineExampleEvent')
                      ->disableOriginalConstructor()
                      ->getMock();
        $event->expects($this->once())
              ->method('getOutline')
              ->will($this->returnValue($node));

        $listener = new EventListener($this->config, $this->container, $this->service);
        $listener->beforeScenario($event);
    }

    public function testAfterScenario()
    {
        $coverage = $this->getMockBuilder('PHP_CodeCoverage')
                         ->disableOriginalConstructor()
                         ->getMock();
        $coverage->expects($this->once())
                 ->method('stop');

        $this->container->expects($this->once())
                        ->method('get')
                        ->with('code_coverage.driver')
                        ->will($this->returnValue($coverage));

        $event = $this->getMockBuilder('Behat\Behat\Event\ScenarioEvent')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->config, $this->container, $this->service);
        $listener->afterScenario($event);
    }
}

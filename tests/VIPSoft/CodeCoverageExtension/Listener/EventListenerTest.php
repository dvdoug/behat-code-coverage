<?php
/**
 * Event Listener
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Listener;

use VIPSoft\TestCase;
use Behat\Testwork\Tester\Event\SuiteTested;
use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioNode;

/**
 * Event listener test
 *
 * @group Unit
 */
class EventListenerTest extends TestCase
{
    private $coverage;
    private $service;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->coverage = $this->getMock('PHP_CodeCoverage');

        $this->service  = $this->getMockBuilder('VIPSoft\CodeCoverageExtension\Service\ReportService')
                               ->disableOriginalConstructor()
                               ->getMock();
    }

    public function testGetSubscribedEvents()
    {
        $listener = new EventListener($this->coverage, $this->service);
        $events   = $listener->getSubscribedEvents();

        $this->assertTrue(is_array($events));
        $this->assertCount(5, $events);
    }

    public function testBeforeSuite()
    {
        $this->coverage->expects($this->once())
                       ->method('clear');

        $event = $this->getMockBuilder('Behat\Testwork\Tester\Event\SuiteTested')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->coverage, $this->service);
        $listener->beforeSuite($event);
    }

    public function testAfterSuite()
    {
        $this->service->expects($this->once())
                      ->method('generateReport');

        $event = $this->getMockBuilder('Behat\Testwork\Tester\Event\SuiteTested')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->coverage, $this->service);
        $listener->afterSuite($event);
    }

    public function testBeforeScenario()
    {
        $feature = new FeatureNode('featureNode', 'A Feature', array(), null, array(), 'Feature', 'en', 'MyFile.feature', 0);

        $event = $this->getMockBuilder('Behat\Behat\Tester\Event\FeatureTested')
                      ->disableOriginalConstructor()
                      ->getMock();
        $event->expects($this->exactly(2))
              ->method('getFeature')
              ->will($this->returnValue($feature));

        $listener = new EventListener($this->coverage, $this->service);
        $listener->beforeFeature($event);

        $this->coverage->expects($this->once())
                       ->method('start')
                       ->with('MyFile.feature:1');

        $node = new ScenarioNode('scenarioNode', array(), array(), 'Scenario', 1);

        $event = $this->getMockBuilder('Behat\Behat\Tester\Event\ScenarioTested')
                      ->disableOriginalConstructor()
                      ->getMock();
        $event->expects($this->once())
              ->method('getScenario')
              ->will($this->returnValue($node));

        $listener->beforeScenario($event);
    }

    public function testBeforeExample()
    {
        $this->coverage->expects($this->once())
                       ->method('start')
                       ->with('(unknown):1');

        $node = new OutlineNode('outlineNode', array(), array(), new ExampleTableNode(array(), 'Example'), 'Outline', 1);

        $event = $this->getMockBuilder('Behat\Behat\Tester\Event\ExampleTested')
                      ->disableOriginalConstructor()
                      ->getMock();
        $event->expects($this->once())
              ->method('getScenario')
              ->will($this->returnValue($node));

        $listener = new EventListener($this->coverage, $this->service);
        $listener->beforeScenario($event);
    }

    public function testAfterScenario()
    {
        $this->coverage->expects($this->once())
                       ->method('stop');

        $event = $this->getMockBuilder('Behat\Behat\Tester\Event\ScenarioTested')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->coverage, $this->service);
        $listener->afterScenario($event);
    }
}

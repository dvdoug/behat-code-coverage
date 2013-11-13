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
        $this->assertCount(4, $events);
        $this->assertEquals('beforeSuite', $events['beforeSuite']);
        $this->assertEquals('afterSuite', $events['afterSuite']);
        $this->assertEquals('beforeScenario', $events['beforeScenario']);
        $this->assertEquals('afterScenario', $events['afterScenario']);
    }

    public function testBeforeSuite()
    {
        $this->coverage->expects($this->once())
                       ->method('clear');

        $event = $this->getMockBuilder('Behat\Behat\Event\SuiteEvent')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->coverage, $this->service);
        $listener->beforeSuite($event);
    }

    public function testAfterSuite()
    {
        $this->service->expects($this->once())
                      ->method('generateReport');

        $event = $this->getMockBuilder('Behat\Behat\Event\SuiteEvent')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->coverage, $this->service);
        $listener->afterSuite($event);
    }

    public function testBeforeScenario()
    {
        $this->coverage->expects($this->once())
                       ->method('start')
                       ->with('MyFile.feature:1');

        $feature = new FeatureNode('featureNode', 'A Feature', 'MyFile.feature', 0);

        $node = new ScenarioNode('scenarioNode', 1);
        $node->setFeature($feature);

        $event = $this->getMockBuilder('Behat\Behat\Event\ScenarioEvent')
                      ->disableOriginalConstructor()
                      ->getMock();
        $event->expects($this->once())
              ->method('getScenario')
              ->will($this->returnValue($node));

        $listener = new EventListener($this->coverage, $this->service);
        $listener->beforeScenario($event);
    }

    public function testBeforeOutlineExample()
    {
        $this->coverage->expects($this->once())
                       ->method('start')
                       ->with('(unknown):1');

        $node = new OutlineNode('outlineNode', 1);

        $event = $this->getMockBuilder('Behat\Behat\Event\OutlineExampleEvent')
                      ->disableOriginalConstructor()
                      ->getMock();
        $event->expects($this->once())
              ->method('getOutline')
              ->will($this->returnValue($node));

        $listener = new EventListener($this->coverage, $this->service);
        $listener->beforeScenario($event);
    }

    public function testAfterScenario()
    {
        $this->coverage->expects($this->once())
                       ->method('stop');

        $event = $this->getMockBuilder('Behat\Behat\Event\ScenarioEvent')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->coverage, $this->service);
        $listener->afterScenario($event);
    }
}

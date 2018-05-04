<?php
/**
 * Event Listener
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Listener;

use VIPSoft\TestCase;
use Behat\Gherkin\Node\ExampleTableNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioNode;

/**
 * Event listener test
 *
 * @group Unit
 */

/**
 * TODO - reimplement integration tests'
 *
class EventListenerTest extends TestCase
{
    private $coverage;
    private $service;

    /**
     * {@inheritdoc}
    protected function setUp()
    {
        parent::setUp();

        $this->coverage = $this->createMock('SebastianBergmann\CodeCoverage\CodeCoverage');

        $this->service  = $this->getMockBuilder('LeanPHP\Behat\CodeCoverage\Service\ReportService')
                               ->disableOriginalConstructor()
                               ->getMock();
    }

    public function testGetSubscribedEvents()
    {
        $listener = new EventListener($this->coverage, $this->service);
        $events   = $listener->getSubscribedEvents();

        $this->assertTrue(is_array($events));
        $this->assertCount(6, $events);
    }

    public function testBeforeExercise()
    {
        $this->coverage->expects($this->once())
                       ->method('clear');

        $event = $this->getMockBuilder('Behat\Testwork\EventDispatcher\Event\ExerciseCompleted')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->coverage, $this->service);
        $listener->beforeExercise($event);
    }

    public function testAfterExercise()
    {
        $this->service->expects($this->once())
                      ->method('generateReport');

        $event = $this->getMockBuilder('Behat\Testwork\EventDispatcher\Event\ExerciseCompleted')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->coverage, $this->service);
        $listener->afterExercise($event);
    }

    public function testBeforeScenario()
    {
        $this->coverage->expects($this->once())
                       ->method('start')
                       ->with('MyFile.feature:1');

        $node = new ScenarioNode('scenarioNode', array(), array(), 'Scenario', 1);

        $feature = new FeatureNode('featureNode', 'A Feature', array(), null, array(), 'Feature', 'en', 'MyFile.feature', 0);

        $event = $this->getMockBuilder('Behat\Behat\EventDispatcher\Event\ScenarioTested')
                      ->disableOriginalConstructor()
                      ->getMock();

        $event->expects($this->once())
              ->method('getScenario')
              ->will($this->returnValue($node));

        $event->expects($this->once())
              ->method('getFeature')
              ->will($this->returnValue($feature));

        $listener = new EventListener($this->coverage, $this->service);
        $listener->beforeScenario($event);
    }

    public function testBeforeExample()
    {
        $this->coverage->expects($this->once())
                       ->method('start')
                       ->with('MyFile.feature:2');

        $node = new OutlineNode('outlineNode', array(), array(), new ExampleTableNode(array(), 'Example'), 'Outline', 2);

        $feature = new FeatureNode('featureNode', 'A Feature', array(), null, array(), 'Feature', 'en', 'MyFile.feature', 0);

        $event = $this->getMockBuilder('Behat\Behat\EventDispatcher\Event\ScenarioTested')
                      ->disableOriginalConstructor()
                      ->getMock();

        $event->expects($this->once())
              ->method('getScenario')
              ->will($this->returnValue($node));

        $event->expects($this->once())
              ->method('getFeature')
              ->will($this->returnValue($feature));

        $listener = new EventListener($this->coverage, $this->service);
        $listener->beforeScenario($event);
    }

    public function testAfterScenario()
    {
        $this->coverage->expects($this->once())
                       ->method('stop');

        $event = $this->getMockBuilder('Behat\Behat\EventDispatcher\Event\ScenarioTested')
                      ->disableOriginalConstructor()
                      ->getMock();

        $listener = new EventListener($this->coverage, $this->service);
        $listener->afterScenario($event);
    }
}*/

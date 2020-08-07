<?php

declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Test;

use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use DVDoug\Behat\CodeCoverage\Service\ReportService;
use DVDoug\Behat\CodeCoverage\Subscriber\EventSubscriber;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\RawCodeCoverageData;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventSubscriberTest extends TestCase
{
    use ProphecyTrait;

    public function testCanSubscribeToEvents(): void
    {
        $eventSubscriber = new EventSubscriber(new ReportService([]));
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($eventSubscriber);

        $listeners = $dispatcher->getListeners();

        self::assertEquals(array_keys($eventSubscriber::getSubscribedEvents()), array_keys($listeners));
    }

    public function testBeforeScenarioWithNoCoverage(): void
    {
        $event = $this->createMock(ScenarioTested::class);

        $subscriber = new EventSubscriber(new ReportService([]));
        $subscriber->beforeScenario($event);

        self::assertTrue(true);
    }

    public function testAfterScenarioWithNoCoverage(): void
    {
        $event = $this->createMock(ScenarioTested::class);

        $subscriber = new EventSubscriber(new ReportService([]));
        $subscriber->afterScenario($event);

        self::assertTrue(true);
    }

    public function testAfterExerciseWithNoCoverage(): void
    {
        $event = $this->createMock(ExerciseCompleted::class);

        $subscriber = new EventSubscriber(new ReportService([]));
        $subscriber->afterExercise($event);

        self::assertTrue(true);
    }

    public function testScenarioWithCoverage(): void
    {
        $driverClassReflection = new \ReflectionClass(Driver::class);

        $scenario = $this->createMock(ScenarioNode::class);
        $scenario->method('getLine')->willReturn(123);

        $feature = $this->createMock(FeatureNode::class);
        $feature->method('getFile')->willReturn('/tmp/file');

        $event = $this->createMock(ScenarioTested::class);
        $event->method('getScenario')->willReturn($scenario);
        $event->method('getFeature')->willReturn($feature);

        $driver = $this->prophesize(Driver::class);
        if ($driverClassReflection->isInterface()) {
            $driver->start(true)->shouldBeCalled();
        } else {
            $driver->start()->shouldBeCalled();
        }

        $codeCoverage = new CodeCoverage($driver->reveal(), new Filter());

        $subscriber = new EventSubscriber(new ReportService([]), $codeCoverage);
        $subscriber->beforeScenario($event);

        if ($driverClassReflection->isInterface()) {
            $driver->stop()->willReturn([]);
        } else {
            $driver->stop()->willReturn(RawCodeCoverageData::fromXdebugWithPathCoverage([]));
        }
        $driver->stop()->shouldBeCalled();

        $subscriber = new EventSubscriber(new ReportService([]), $codeCoverage);
        $subscriber->afterScenario($event);
    }

    public function testAfterExerciseWithCoverage(): void
    {
        $event = $this->createMock(ExerciseCompleted::class);

        $driver = $this->prophesize(Driver::class);

        $codeCoverage = new CodeCoverage($driver->reveal(), new Filter());

        $reportService = $this->prophesize(ReportService::class);
        $reportService->generateReport($codeCoverage)->shouldBeCalled();

        $subscriber = new EventSubscriber($reportService->reveal(), $codeCoverage);
        $subscriber->afterExercise($event);
    }
}

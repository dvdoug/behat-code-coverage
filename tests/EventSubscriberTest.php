<?php
/**
 * Behat Code Coverage
 */
declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Test;

use function array_keys;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use DVDoug\Behat\CodeCoverage\Service\ReportService;
use DVDoug\Behat\CodeCoverage\Subscriber\EventSubscriber;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\RawCodeCoverageData;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventSubscriberTest extends TestCase
{
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
        $scenario = $this->createMock(ScenarioNode::class);
        $scenario->method('getLine')->willReturn(123);

        $feature = $this->createMock(FeatureNode::class);
        $feature->method('getFile')->willReturn('/tmp/file');

        $event = $this->createMock(ScenarioTested::class);
        $event->method('getScenario')->willReturn($scenario);
        $event->method('getFeature')->willReturn($feature);

        $driver = $this->createMock(Driver::class);
        $driver->expects(self::once())->method('start');
        $driver->method('stop')->willReturn(RawCodeCoverageData::fromXdebugWithPathCoverage([]));

        $codeCoverage = new CodeCoverage($driver, new Filter());

        $subscriber = new EventSubscriber(new ReportService([]), $codeCoverage);
        $subscriber->beforeScenario($event);

        $subscriber = new EventSubscriber(new ReportService([]), $codeCoverage);
        $subscriber->afterScenario($event);
    }

    public function testAfterExerciseWithCoverage(): void
    {
        $event = $this->createMock(ExerciseCompleted::class);

        $driver = $this->createMock(Driver::class);

        $codeCoverage = new CodeCoverage($driver, new Filter());

        $reportService = $this->createMock(ReportService::class);
        $reportService->expects(self::once())->method('generateReport')->with($codeCoverage);

        $subscriber = new EventSubscriber($reportService, $codeCoverage);
        $subscriber->afterExercise($event);
    }
}

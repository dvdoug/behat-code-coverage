<?php
/**
 * Behat Code Coverage
 */
declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Test;

use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use DVDoug\Behat\CodeCoverage\Service\ReportService;
use DVDoug\Behat\CodeCoverage\Subscriber\EventSubscriber;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Data\RawCodeCoverageData as RawCodeCoverageDataV10;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\RawCodeCoverageData as RawCodeCoverageDataV9;
use Symfony\Component\EventDispatcher\EventDispatcher;

use function array_keys;
use function class_exists;

class EventSubscriberTest extends TestCase
{
    public function testCanSubscribeToEvents(): void
    {
        $eventSubscriber = new EventSubscriber(new ReportService([]), null);
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($eventSubscriber);

        $listeners = $dispatcher->getListeners();

        self::assertEquals(array_keys($eventSubscriber::getSubscribedEvents()), array_keys($listeners));
    }

    public function testBeforeScenarioWithNoCoverage(): void
    {
        $event = $this->createMock(ScenarioTested::class);

        $subscriber = new EventSubscriber(new ReportService([]), null);
        $subscriber->beforeScenario($event);

        self::assertTrue(true);
    }

    public function testAfterScenarioWithNoCoverage(): void
    {
        $event = $this->createMock(ScenarioTested::class);

        $subscriber = new EventSubscriber(new ReportService([]), null);
        $subscriber->afterScenario($event);

        self::assertTrue(true);
    }

    public function testAfterExerciseWithNoCoverage(): void
    {
        $event = $this->createMock(ExerciseCompleted::class);

        $subscriber = new EventSubscriber(new ReportService([]), null);
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
        if (class_exists(RawCodeCoverageDataV9::class)) {
            $driver->method('stop')->willReturn(RawCodeCoverageDataV9::fromXdebugWithPathCoverage([]));
        } else {
            $driver->method('stop')->willReturn(RawCodeCoverageDataV10::fromXdebugWithPathCoverage([]));
        }

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

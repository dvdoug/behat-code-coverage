<?php

declare(strict_types=1);

namespace DVDoug\Behat\CodeCoverage\Test;

use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use DVDoug\Behat\CodeCoverage\Service\ReportService;
use DVDoug\Behat\CodeCoverage\Subscriber\EventSubscriber;
use PHPUnit\Framework\TestCase;
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
}

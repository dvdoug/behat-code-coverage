<?php

declare(strict_types=1);
/**
 * Event Listener.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-2-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use DVDoug\Behat\CodeCoverage\Service\ReportService;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class EventListener implements EventSubscriberInterface
{
    /**
     * @var CodeCoverage
     */
    private $coverage;

    /**
     * @var ReportService
     */
    private $reportService;

    /**
     * Constructor.
     */
    public function __construct(ReportService $reportService, CodeCoverage $coverage = null)
    {
        $this->reportService = $reportService;
        $this->coverage = $coverage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::BEFORE => 'beforeScenario',
            ExampleTested::BEFORE => 'beforeScenario',
            ScenarioTested::AFTER => 'afterScenario',
            ExampleTested::AFTER => 'afterScenario',
            ExerciseCompleted::AFTER => 'afterExercise',
        ];
    }

    /**
     * Before Scenario/Outline Example hook.
     */
    public function beforeScenario(ScenarioTested $event): void
    {
        if (!$this->coverage) {
            return;
        }

        $node = $event->getScenario();
        $id = $event->getFeature()->getFile() . ':' . $node->getLine();

        $this->coverage->start($id);
    }

    /**
     * After Scenario/Outline Example hook.
     */
    public function afterScenario(ScenarioTested $event): void
    {
        if (!$this->coverage) {
            return;
        }

        $this->coverage->stop();
    }

    /**
     * After Exercise hook.
     */
    public function afterExercise(ExerciseCompleted $event): void
    {
        if (!$this->coverage) {
            return;
        }

        $this->reportService->generateReport($this->coverage);
    }
}

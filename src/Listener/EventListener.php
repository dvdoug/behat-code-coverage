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
     * @var \DVDoug\Behat\CodeCoverage\Service\ReportService
     */
    private $reportService;

    /**
     * @var bool
     */
    private $skipCoverage;

    /**
     * Constructor.
     *
     * @param CodeCoverage                                     $coverage
     * @param \DVDoug\Behat\CodeCoverage\Service\ReportService $reportService
     * @param bool                                             $skipCoverage
     */
    public function __construct(CodeCoverage $coverage, ReportService $reportService, $skipCoverage = false)
    {
        $this->coverage = $coverage;
        $this->reportService = $reportService;
        $this->skipCoverage = $skipCoverage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ExerciseCompleted::BEFORE => 'beforeExercise',
            ScenarioTested::BEFORE => 'beforeScenario',
            ExampleTested::BEFORE => 'beforeScenario',
            ScenarioTested::AFTER => 'afterScenario',
            ExampleTested::AFTER => 'afterScenario',
            ExerciseCompleted::AFTER => 'afterExercise',
        ];
    }

    /**
     * Before Exercise hook.
     *
     * @param \Behat\Testwork\EventDispatcher\Event\ExerciseCompleted $event
     */
    public function beforeExercise(ExerciseCompleted $event): void
    {
        if ($this->skipCoverage) {
            return;
        }

        $this->coverage->clear();
    }

    /**
     * Before Scenario/Outline Example hook.
     *
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function beforeScenario(ScenarioTested $event): void
    {
        if ($this->skipCoverage) {
            return;
        }

        $node = $event->getScenario();
        $id = $event->getFeature()->getFile() . ':' . $node->getLine();

        $this->coverage->start($id);
    }

    /**
     * After Scenario/Outline Example hook.
     *
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function afterScenario(ScenarioTested $event): void
    {
        if ($this->skipCoverage) {
            return;
        }

        $this->coverage->stop();
    }

    /**
     * After Exercise hook.
     *
     * @param \Behat\Testwork\Tester\Event\ExerciseCompleted $event
     */
    public function afterExercise(ExerciseCompleted $event): void
    {
        if ($this->skipCoverage) {
            return;
        }

        $this->reportService->generateReport($this->coverage);
    }
}

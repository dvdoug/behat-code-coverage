<?php
/**
 * Event Listener
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use LeanPHP\Behat\CodeCoverage\Service\ReportService;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
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
     * @var \LeanPHP\Behat\CodeCoverage\Service\ReportService
     */
    private $reportService;

    /**
     * @var bool
     */
    private $skipCoverage;

    /**
     * Constructor
     *
     * @param CodeCoverage                                      $coverage
     * @param \LeanPHP\Behat\CodeCoverage\Service\ReportService $reportService
     * @param boolean                                           $skipCoverage
     */
    public function __construct(CodeCoverage $coverage, ReportService $reportService, $skipCoverage = false)
    {
        $this->coverage      = $coverage;
        $this->reportService = $reportService;
        $this->skipCoverage  = $skipCoverage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ExerciseCompleted::BEFORE => 'beforeExercise',
            ScenarioTested::BEFORE => 'beforeScenario',
            ExampleTested::BEFORE  => 'beforeScenario',
            ScenarioTested::AFTER  => 'afterScenario',
            ExampleTested::AFTER   => 'afterScenario',
            ExerciseCompleted::AFTER => 'afterExercise',
        );
    }

    /**
     * Before Exercise hook
     *
     * @param \Behat\Testwork\EventDispatcher\Event\ExerciseCompleted $event
     */
    public function beforeExercise(ExerciseCompleted $event)
    {
        if ($this->skipCoverage) {
            return;
        }

        $this->coverage->clear();
    }

    /**
     * Before Scenario/Outline Example hook
     *
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function beforeScenario(ScenarioTested $event)
    {
        if ($this->skipCoverage) {
            return;
        }

        $node = $event->getScenario();
        $id   = $event->getFeature()->getFile().':'.$node->getLine();

        $this->coverage->start($id);
    }

    /**
     * After Scenario/Outline Example hook
     *
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function afterScenario(ScenarioTested $event)
    {
        if ($this->skipCoverage) {
            return;
        }

        $this->coverage->stop();
    }

    /**
     * After Exercise hook
     *
     * @param \Behat\Testwork\Tester\Event\ExerciseCompleted $event
     */
    public function afterExercise(ExerciseCompleted $event)
    {
        if ($this->skipCoverage) {
            return;
        }

        $this->reportService->generateReport($this->coverage);
    }
}

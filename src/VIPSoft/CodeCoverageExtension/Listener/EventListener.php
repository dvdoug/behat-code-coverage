<?php
/**
 * Event Listener
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VIPSoft\CodeCoverageExtension\Service\ReportService;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Testwork\EventDispatcher\Event\BeforeTested;
use Behat\Testwork\EventDispatcher\Event\AfterTested;
use Behat\Testwork\EventDispatcher\Event\BeforeExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\AfterExerciseCompleted;

/**
 * Event listener
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class EventListener implements EventSubscriberInterface
{
    /**
     * @var \PHP_CodeCoverage
     */
    private $coverage;

    /**
     * @var \VIPSoft\CodeCoverageExtension\Service\ReportService
     */
    private $reportService;

    /**
     * Constructor
     *
     * @param \PHP_CodeCoverage                                    $coverage
     * @param \VIPSoft\CodeCoverageExtension\Service\ReportService $reportService
     */
    public function __construct(\PHP_CodeCoverage $coverage, ReportService $reportService)
    {
        $this->coverage      = $coverage;
        $this->reportService = $reportService;
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
     * @param \Behat\Testwork\EventDispatcher\Event\BeforeExerciseCompleted $event
     */
    public function beforeExercise(BeforeExerciseCompleted $event)
    {
        $this->coverage->clear();
    }

    /**
     * Before Scenario/Outline Example hook
     *
     * @param \Behat\Behat\EventDispatcher\Event\BeforeTested $event
     */
    public function beforeScenario(BeforeTested $event)
    {
        $node = $event->getScenario();
        $id   = $event->getFeature()->getFile() . ':' . $node->getLine();

        $this->coverage->start($id);
    }

    /**
     * After Scenario/Outline Example hook
     *
     * @param \Behat\Behat\EventDispatcher\Event\AfterTested $event
     */
    public function afterScenario(AfterTested $event)
    {
        $this->coverage->stop();
    }

    /**
     * After Exercise hook
     *
     * @param \Behat\Testwork\Tester\Event\AfterExerciseCompleted $event
     */
    public function afterExercise(AfterExerciseCompleted $event)
    {
        $this->reportService->generateReport($this->coverage);
    }
}

<?php
/**
 * Event Listener
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Listener;

use Behat\Behat\Tester\Event\AbstractScenarioTested;
use Behat\Behat\Tester\Event\FeatureTested;
use Behat\Behat\Tester\Event\ScenarioTested;
use Behat\Testwork\Tester\Event\SuiteTested;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VIPSoft\CodeCoverageExtension\Service\ReportService;

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
     * @var string
     */
    private $file;

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
        $this->file          = '(unknown)';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            SuiteTested::BEFORE    => 'beforeSuite',
            FeatureTested::BEFORE  => 'beforeFeature',
            ScenarioTested::BEFORE => 'beforeScenario',
            ScenarioTested::AFTER  => 'afterScenario',
            SuiteTested::AFTER     => 'afterSuite',
        );
    }

    /**
     * Before Suite hook
     *
     * @param \Behat\Testwork\Tester\Event\SuiteTested $event
     */
    public function beforeSuite(SuiteTested $event)
    {
        $this->coverage->clear();
    }

    /**
     * Before Feature hook
     *
     * @param \Behat\Behat\Tester\Event\FeatureTested $event
     */
    public function beforeFeature(FeatureTested $event)
    {
        $this->file = $event->getFeature() ? $event->getFeature()->getFile() : '(unknown)';
    }

    /**
     * Before Scenario/Outline Example hook
     *
     * @param \Behat\Behat\Tester\Event\AbstractScenarioTested $event
     */
    public function beforeScenario(AbstractScenarioTested $event)
    {
        $node = $event->getScenario();
        $id = $this->file . ':' . $node->getLine();

        $this->coverage->start($id);
    }

    /**
     * After Scenario/Outline Example hook
     *
     * @param \Behat\Behat\Tester\Event\AbstractScenarioTested $event
     */
    public function afterScenario(AbstractScenarioTested $event)
    {
        $this->coverage->stop();
    }

    /**
     * After Suite hook
     *
     * @param \Behat\Testwork\Tester\Event\SuiteTested $event
     */
    public function afterSuite(SuiteTested $event)
    {
        $this->reportService->generateReport($this->coverage);
    }
}

<?php
/**
 * Event Listener
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Listener;

use Behat\Behat\Event\BaseScenarioEvent;
use Behat\Behat\Event\OutlineExampleEvent;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\SuiteEvent;
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
            'beforeSuite'    => 'beforeSuite',
            'beforeScenario' => 'beforeScenario',
            'afterScenario'  => 'afterScenario',
            'afterSuite'     => 'afterSuite',
        );
    }

    /**
     * Before Suite hook
     *
     * @param \Behat\Behat\Event\SuiteEvent $event
     */
    public function beforeSuite(SuiteEvent $event)
    {
        $this->coverage->clear();
    }

    /**
     * Before Scenario/OutlineExample hook
     *
     * @param \Behat\Behat\Event\BaseScenarioEvent $event
     */
    public function beforeScenario(BaseScenarioEvent $event)
    {
        if ($event instanceof OutlineExampleEvent) {
            $node = $event->getOutline();
        } elseif ($event instanceof ScenarioEvent) {
            $node = $event->getScenario();
        }

        $file = $node->getFeature() ? $node->getFeature()->getFile() : '(unknown)';
        $id = $file . ':' . $node->getLine();

        $this->coverage->start($id);
    }

    /**
     * After Scenario/OutlineExample hook
     *
     * @param \Behat\Behat\Event\BaseScenarioEvent $event
     */
    public function afterScenario(BaseScenarioEvent $event)
    {
        $this->coverage->stop();
    }

    /**
     * After Suite hook
     *
     * @param \Behat\Behat\Event\SuiteEvent $event
     */
    public function afterSuite(SuiteEvent $event)
    {
        $this->reportService->generateReport($this->coverage);
    }
}

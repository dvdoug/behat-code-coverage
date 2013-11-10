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
     * array
     */
    private $config;

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
     * @param array                                                     $config
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \VIPSoft\CodeCoverageExtension\Service\ReportService      $reportService
     */
    public function __construct(array $config, ContainerInterface $container, ReportService $reportService)
    {
        $driverService       = $config['driver']['service'];
        $this->config        = $config;
        $this->coverage      = $container->get($driverService);
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

        $id = $node->getTitle() . ':' . $node->getLine();

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

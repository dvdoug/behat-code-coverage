<?php
/**
 * Suite Listener
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Listener;

use Behat\Behat\Event\SuiteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VIPSoft\CodeCoverageExtension\Model\Aggregate;

/**
 * Suite event listener
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class SuiteListener implements EventSubscriberInterface
{
    /**
     * array
     */
    private $config;

    /**
     * @var array
     */
    private $drivers;

    /**
     * @var \VIPSoft\CodeCoverageExtension\Service\ReportService
     */
    private $reportService;

    /**
     * Constructor
     *
     * @param array                                                $config
     * @param \PHP_CodeCoverage_Driver                             $remoteDriver
     * @param \PHP_CodeCoverage_Driver                             $localDriver
     * @param \VIPSoft\CodeCoverageExtension\Service\ReportService $reportService
     */
    public function __construct(array $config, \PHP_CodeCoverage_Driver $remoteDriver, \PHP_CodeCoverage_Driver $localDriver, \VIPSoft\CodeCoverageExtension\Service\ReportService $reportService)
    {
        $this->config = $config;

        $this->drivers = array(
            'remote' => $remoteDriver,
            'local'  => $localDriver,
        );

        $this->reportService = $reportService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'beforeSuite' => 'beforeSuite',
            'afterSuite'  => 'afterSuite',
        );
    }

    /**
     * Before Suite hook
     *
     * @param \Behat\Behat\Event\SuiteEvent $event
     */
    public function beforeSuite(SuiteEvent $event)
    {
        foreach ($this->drivers as $type => $driver) {
            if ( ! in_array($type, $this->config['drivers'])) {
                continue;
            }

            $driver->start();
        }
    }

    /**
     * After Suite hook
     *
     * @param \Behat\Behat\Event\SuiteEvent $event
     */
    public function afterSuite(SuiteEvent $event)
    {
        $aggregate = new Aggregate;

        foreach ($this->drivers as $type => $driver) {
            if ( ! in_array($type, $this->config['drivers'])) {
                continue;
            }

            $coverage = $driver->stop();

            foreach ($coverage as $class => $counts) {
                $aggregate->update($class, $counts);
            }
        }

        $this->reportService->generateReport($aggregate->getCoverage());
    }
}

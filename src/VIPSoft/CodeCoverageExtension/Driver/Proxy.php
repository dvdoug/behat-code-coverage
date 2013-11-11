<?php
/**
 * Proxy Code Coverage Driver
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Driver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use VIPSoft\CodeCoverageExtension\Model\Aggregate;

/**
 * Proxy driver
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Proxy implements \PHP_CodeCoverage_Driver
{
    /**
     * @var array
     */
    private $drivers;

    /**
     * Constructor
     *
     * @param array                                                     $config
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(array $config, ContainerInterface $container)
    {
        $this->drivers = array();

        foreach ($config['drivers'] as $type) {
            $serviceName = 'behat.code_coverage.driver.' . $type;

            $this->drivers[] = $container->get($serviceName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        foreach ($this->drivers as $driver) {
            $driver->start();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $aggregate = new Aggregate;

        foreach ($this->drivers as $driver) {
            $coverage = $driver->stop();

            foreach ($coverage as $class => $counts) {
                $aggregate->update($class, $counts);
            }
        }

        return $aggregate->getCoverage();
    }
}

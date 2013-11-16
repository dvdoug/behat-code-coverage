<?php
/**
 * Proxy Code Coverage Driver
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Driver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use VIPSoft\CodeCoverageCommon\Model\Aggregate;
use PHP_CodeCoverage_Driver as DriverInterface;

/**
 * Proxy driver
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Proxy implements DriverInterface
{
    /**
     * @var array
     */
    private $drivers = array();

    /**
     * Register driver
     *
     * @param DriverInterface|null $driver
     */
    public function addDriver(DriverInterface $driver = null)
    {
        if ($driver) {
            $this->drivers[] = $driver;
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

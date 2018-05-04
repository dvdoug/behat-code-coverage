<?php
/**
 * Proxy Code Coverage Driver
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Driver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use LeanPHP\Behat\CodeCoverage\Common\Model\Aggregate;
use SebastianBergmann\CodeCoverage\Driver\Driver as DriverInterface;

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
    public function start(bool $determineUnusedAndDead = true): void
    {
        foreach ($this->drivers as $driver) {
            $driver->start($determineUnusedAndDead);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stop(): array
    {
        $aggregate = new Aggregate();

        foreach ($this->drivers as $driver) {
            $coverage = $driver->stop();

            if (! $coverage) {
                continue;
            }

            foreach ($coverage as $class => $counts) {
                $aggregate->update($class, $counts);
            }
        }

        return $aggregate->getCoverage();
    }
}

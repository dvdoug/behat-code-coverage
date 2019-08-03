<?php

declare(strict_types=1);
/**
 * Proxy Code Coverage Driver.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-2-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Driver;

use DVDoug\Behat\CodeCoverage\Common\Model\Aggregate;
use SebastianBergmann\CodeCoverage\Driver\Driver as DriverInterface;

/**
 * Proxy driver.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Proxy implements DriverInterface
{
    /**
     * @var array
     */
    private $drivers = [];

    /**
     * Register driver.
     *
     * @param DriverInterface|null $driver
     */
    public function addDriver(DriverInterface $driver = null): void
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

            if (!$coverage) {
                continue;
            }

            foreach ($coverage as $class => $counts) {
                $aggregate->update($class, $counts);
            }
        }

        return $aggregate->getCoverage();
    }
}

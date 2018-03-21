<?php
/**
 * Code Coverage Stub Driver
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Driver;

use SebastianBergmann\CodeCoverage\Driver\Driver as DriverInterface;

/**
 * Stub driver
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Stub implements DriverInterface
{
    private $driver;

    /**
     * Register driver
     *
     * @param DriverInterface $driver
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Get driver
     *
     * @return DriverInterface $driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * {@inheritdoc}
     */
    public function start($determineUnusedAndDead = true)
    {
        if ($this->driver) {
            $this->driver->start();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        return $this->driver ? $this->driver->stop() : false;
    }
}

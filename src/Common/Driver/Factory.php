<?php

declare(strict_types=1);
/**
 * Code Coverage Driver Factory.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-3-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Common\Driver;

use SebastianBergmann\CodeCoverage\Driver\Driver as DriverInterface;

/**
 * Driver factory.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Factory
{
    /**
     * @var array
     */
    private $driverClasses;

    /**
     * Constructor.
     *
     * @param array $driverClasses List of namespaced driver classes
     */
    public function __construct(array $driverClasses)
    {
        $this->driverClasses = $driverClasses;
    }

    /**
     * Create driver.
     *
     * @return DriverInterface
     */
    public function create()
    {
        foreach ($this->driverClasses as $driverClass) {
            try {
                $driver = new $driverClass();

                return $driver;
            } catch (\Exception $e) {
            }
        }

        return null;
    }
}

<?php
/**
 * XCache Code Coverage Driver
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Driver;

use SebastianBergmann\CodeCoverage\Driver\Driver as DriverInterface;

/**
 * XCache Driver
 *
 * {@internal Derived from PHP_CodeCoverage_Driver_Xdebug.}
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class XCache implements DriverInterface
{
    /**
     * Constructor
     *
     * @throws \PHP_CodeCoverage_Exception if PHP code coverage not enabled
     */
    public function __construct()
    {
        if ( ! extension_loaded('xcache')) {
            throw new \SebastianBergmann\CodeCoverage\Exception('This driver requires XCache');
        }

        if (version_compare(phpversion('xcache'), '1.2.0', '<') ||
            ! ini_get('xcache.coverager')
        ) {
            throw new \PHP_CodeCoverage_Exception('xcache.coverager=On has to be set in php.ini');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function start($determineUnusedAndDead = true)
    {
        xcache_coverager_start();
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $codeCoverage = xcache_coverager_get();

        xcache_coverager_stop(true);

        return $codeCoverage;
    }
}

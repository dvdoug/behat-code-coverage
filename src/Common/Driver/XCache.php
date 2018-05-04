<?php
/**
 * XCache Code Coverage Driver
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Driver;

use SebastianBergmann\CodeCoverage\Driver\Driver as DriverInterface;

/**
 * XCache Driver
 *
 * {@internal Derived from SebastianBergmann\CodeCoverage\Driver\Xdebug.}
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class XCache implements DriverInterface
{
    /**
     * Constructor
     *
     * @throws \SebastianBergmann\CodeCoverage\RuntimeException if PHP code coverage not enabled
     */
    public function __construct()
    {
        if (! extension_loaded('xcache')) {
            throw new \SebastianBergmann\CodeCoverage\RuntimeException('This driver requires XCache');
        }

        if (version_compare(phpversion('xcache'), '1.2.0', '<') ||
            ! ini_get('xcache.coverager')
        ) {
            throw new \SebastianBergmann\CodeCoverage\RuntimeException('xcache.coverager=On has to be set in php.ini');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function start($determineUnusedAndDead = true): void
    {
        xcache_coverager_start();
    }

    /**
     * {@inheritdoc}
     */
    public function stop(): array
    {
        $codeCoverage = xcache_coverager_get();

        if (null === $codeCoverage) {
            $codeCoverage = [];
        }

        xcache_coverager_stop(true);

        return $codeCoverage;
    }
}

<?php
/**
 * HHVM Code Coverage Driver
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Driver;

use PHP_CodeCoverage_Driver as DriverInterface;

/**
 * HHVM (Hip Hop VM) Driver
 *
 * {@internal Derived from PHP_CodeCoverage_Driver_Xdebug.}
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class HHVM implements DriverInterface
{
    /**
     * Constructor
     *
     * @throws \PHP_CodeCoverage_Exception if PHP code coverage not enabled
     */
    public function __construct()
    {
        if ( ! defined('HPHP_VERSION')) {
            throw new \PHP_CodeCoverage_Exception('This driver requires HHVM');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        fb_enable_code_coverage();
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $codeCoverage = fb_get_code_coverage(true);

        fb_disable_code_coverage();

        return $codeCoverage;
    }
}

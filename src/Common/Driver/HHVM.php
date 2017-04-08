<?php
/**
 * HHVM Code Coverage Driver
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Driver;

use SebastianBergmann\CodeCoverage\Driver\Driver as DriverInterface;
use SebastianBergmann\CodeCoverage\PHP_CodeCoverage;

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
     * @throws SebastianBergmann\CodeCoverage\Exception if PHP code coverage not enabled
     */
    public function __construct()
    {
        if ( ! defined('HPHP_VERSION')) {
            throw new \SebastianBergmann\CodeCoverage\Exception('This driver requires HHVM');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function start($determineUnusedAndDead = true)
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

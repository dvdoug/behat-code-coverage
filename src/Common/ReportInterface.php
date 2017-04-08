<?php
/**
 * Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common;

use SebastianBergmann\CodeCoverage\CodeCoverage;

/**
 * Report interface
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
interface ReportInterface
{
    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options);

    /**
     * Generate report
     *
     * @param CodeCoverage $coverage
     *
     * @return string|null
     */
    public function process(CodeCoverage $coverage);
}

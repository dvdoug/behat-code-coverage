<?php
/**
 * Code Coverage PHP Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use LeanPHP\Behat\CodeCoverage\Common\ReportInterface;

/**
 * PHP report
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Php implements ReportInterface
{
    /**
     * @var \PHP_CodeCoverage_Report_Clover
     */
    private $report;

    /**
     * @var array
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options)
    {
        if ( ! isset($options['target'])) {
            $options['target'] = null;
        }

        $this->report = new \PHP_CodeCoverage_Report_PHP();
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function process(\PHP_CodeCoverage $coverage)
    {
        return $this->report->process(
            $coverage,
            $this->options['target']
        );
    }
}

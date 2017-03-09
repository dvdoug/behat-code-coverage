<?php
/**
 * Code Coverage HTML Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use LeanPHP\Behat\CodeCoverage\Common\ReportInterface;

/**
 * HTML report
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Html implements ReportInterface
{
    /**
     * @var \PHP_CodeCoverage_Report_HTML
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

        if ( ! isset($options['charset'])) {
            $options['charset'] = 'UTF-8';
        }

        if ( ! isset($options['highlight'])) {
            $options['highlight'] = false;
        }

        if ( ! isset($options['lowUpperBound'])) {
            $options['lowUpperBound'] = 35;
        }

        if ( ! isset($options['highUpperBound'])) {
            $options['highUpperBound'] = 70;
        }

        if ( ! isset($options['generator'])) {
            $options['generator'] = '';
        }

        $this->report = new \PHP_CodeCoverage_Report_HTML(
            $options['charset'],
            $options['highlight'],
            $options['lowUpperBound'],
            $options['highUpperBound'],
            $options['generator']
        );

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

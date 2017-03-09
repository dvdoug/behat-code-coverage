<?php
/**
 * Code Coverage XML Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use LeanPHP\Behat\CodeCoverage\Common\ReportInterface;

/**
 * XML report
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Xml implements ReportInterface
{
    /**
     * @var \PHP_CodeCoverage_Report_XML
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
        if ( ! class_exists('\PHP_CodeCoverage_Report_Xml')) {
            throw new \Exception('XML requires PHP_CodeCoverage 1.3+');
        }

        if ( ! isset($options['target'])) {
            $options['target'] = null;
        }

        $this->report = new \PHP_CodeCoverage_Report_XML();
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

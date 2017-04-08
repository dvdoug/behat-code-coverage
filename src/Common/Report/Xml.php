<?php
/**
 * Code Coverage XML Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use LeanPHP\Behat\CodeCoverage\Common\ReportInterface;
use SebastianBergmann\CodeCoverage\PHP_CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\XML as XMLCC;

/**
 * XML report
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Xml implements ReportInterface
{
    /**
     * @var \SebastianBergmann\CodeCoverage\Report\XML
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
        if ( ! class_exists('XMLCC')) {
            throw new \Exception('XML requires PHP_CodeCoverage 4.0+');
        }

        if ( ! isset($options['target'])) {
            $options['target'] = null;
        }

        $this->report = new XMLCC();
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function process(PHP_CodeCoverage $coverage)
    {
        return $this->report->process(
            $coverage,
            $this->options['target']
        );
    }
}

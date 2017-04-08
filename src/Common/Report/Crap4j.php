<?php
/**
 * Code Coverage Crap4j Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use LeanPHP\Behat\CodeCoverage\Common\ReportInterface;
use SebastianBergmann\CodeCoverage\PHP_CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Crap4j as Crap4jCC;
/**
 * Crap4j report
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Crap4j implements ReportInterface
{
    /**
     * @var Crap4jCC
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
        if ( ! class_exists('Crap4jCC')) {
            throw new \Exception('Crap4j requires PHP_CodeCoverage 4.0+');
        }

        if ( ! isset($options['target'])) {
            $options['target'] = null;
        }

        if ( ! isset($options['name'])) {
            $options['name'] = null;
        }

        $this->report = new Crap4jCC();
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function process(PHP_CodeCoverage $coverage)
    {
        return $this->report->process(
            $coverage,
            $this->options['target'],
            $this->options['name']
        );
    }
}

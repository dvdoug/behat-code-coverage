<?php

declare(strict_types=1);
/**
 * Code Coverage Crap4j Report.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-3-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Common\Report;

use DVDoug\Behat\CodeCoverage\Common\ReportInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Crap4j as Crap4jReport;

/**
 * Crap4j report.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Crap4j implements ReportInterface
{
    /**
     * @var SebastianBergmann\CodeCoverage\Report\Crap4j
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
        if (!class_exists('SebastianBergmann\CodeCoverage\Report\Crap4j')) {
            throw new \Exception('Crap4j requires CodeCoverage 4.0+');
        }

        if (!isset($options['target'])) {
            $options['target'] = null;
        }

        if (!isset($options['name'])) {
            $options['name'] = null;
        }

        $this->report = new Crap4jReport();
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CodeCoverage $coverage)
    {
        return $this->report->process(
            $coverage,
            $this->options['target'],
            $this->options['name']
        );
    }
}

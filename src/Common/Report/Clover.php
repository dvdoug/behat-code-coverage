<?php

declare(strict_types=1);
/**
 * Code Coverage Clover Report.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-3-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Common\Report;

use DVDoug\Behat\CodeCoverage\Common\ReportInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Clover as CloverReport;

/**
 * Clover report.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Clover implements ReportInterface
{
    /**
     * @var \SebastianBergmann\CodeCoverage\Report\Clover
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
        $this->report = new CloverReport();
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

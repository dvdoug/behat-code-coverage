<?php

declare(strict_types=1);
/**
 * Code Coverage Text Report.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-3-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Common\Report;

use DVDoug\Behat\CodeCoverage\Common\ReportInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Text as TextReport;

/**
 * Text report.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Text implements ReportInterface
{
    /**
     * @var \SebastianBergmann\CodeCoverage\Report\Text
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
        if (!isset($options['showColors'])) {
            $options['showColors'] = false;
        }

        if (!isset($options['lowUpperBound'])) {
            $options['lowUpperBound'] = 50;
        }

        if (!isset($options['highLowerBound'])) {
            $options['highLowerBound'] = 90;
        }

        if (!isset($options['showUncoveredFiles'])) {
            $options['showUncoveredFiles'] = false;
        }

        if (!isset($options['showOnlySummary'])) {
            $options['showOnlySummary'] = false;
        }

        $this->report = new TextReport(
            $options['lowUpperBound'],
            $options['highLowerBound'],
            $options['showUncoveredFiles'],
            $options['showOnlySummary']
        );

        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CodeCoverage $coverage)
    {
        return $this->report->process(
            $coverage,
            $this->options['showColors']
        );
    }
}

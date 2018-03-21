<?php
/**
 * Code Coverage Text Report
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use LeanPHP\Behat\CodeCoverage\Common\ReportInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Text as TextReport;

/**
 * Text report
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
        if (! isset($options['showColors'])) {
            $options['showColors'] = false;
        }

        if (! isset($options['printer'])) {
            $options['printer'] = null;
        }

        if (! isset($options['lowUpperBound'])) {
            $options['lowUpperBound'] = 35;
        }

        if (! isset($options['highUpperBound'])) {
            $options['highUpperBound'] = 70;
        }

        if (! isset($options['showUncoveredFiles'])) {
            $options['showUncoveredFiles'] = false;
        }

        if ($this->getVersion() === '1.2') {
            $outputStream = new \PHPUnit_Util_Printer($options['printer']);

            $this->report = new TextReport(
                $outputStream,
                $options['lowUpperBound'],
                $options['highUpperBound'],
                $options['showUncoveredFiles']
            );
        } else {
            if (! isset($options['showOnlySummary'])) {
                $options['showOnlySummary'] = false;
            }

            $this->report = new TextReport(
                $options['lowUpperBound'],
                $options['highUpperBound'],
                $options['showUncoveredFiles'],
                $options['showOnlySummary']
            );
        }

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

    /**
     * return version of CodeCoverage
     *
     * @return string
     */
    private function getVersion()
    {
        $reflectionMethod = new \ReflectionMethod('SebastianBergmann\CodeCoverage\Report\Text', '__construct');
        $parameters = $reflectionMethod->getParameters();

        if (reset($parameters)->name === 'outputStream') {
            return '1.2';
        }

        return '1.3';
    }
}

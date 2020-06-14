<?php

declare(strict_types=1);
/**
 * Code Coverage Report Service.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-2-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Service;

use DVDoug\Behat\CodeCoverage\Common\ReportInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;

/**
 * Code coverage report service.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class ReportService
{
    /**
     * @var array
     */
    private $config;

    /**
     * Constructor.
     */
    public function __construct(array $reportConfig)
    {
        $this->config = $reportConfig;
    }

    /**
     * Generate report.
     */
    public function generateReport(CodeCoverage $coverage): void
    {
        foreach ($this->config as $format => $config) {
            $report = $this->create($format, $config);
            $reportContent = $report->process($coverage);

            if ('text' === $format) {
                echo $reportContent;
            }
        }
    }

    private function create(string $reportType, array $options): ReportInterface
    {
        $className = '\DVDoug\Behat\CodeCoverage\Common\Report\\' . ucfirst($reportType);

        return new $className($options);
    }
}

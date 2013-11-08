<?php
/**
 * Code Coverage Report Service
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Service;

/**
 * Code coverage report service
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
     * @var string
     */
    private $htmlReportWriterClassName;

    /**
     * Constructor
     *
     * @param array  $config
     * @param string $htmlReportWriterClassName
     */
    public function __construct(array $config, $htmlReportWriterClassName = '\PHP_CodeCoverage_Report_HTML')
    {
        $this->config = $config;
        $this->htmlReportWriterClassName = $htmlReportWriterClassName;
    }

    /**
     * Generate report.
     *
     * @param array $coverage
     */
    public function generateReport(array $coverage)
    {
        $writer = new $this->htmlReportWriterClassName();
        $writer->process($coverage, $this->config['output_directory']);
    }
}

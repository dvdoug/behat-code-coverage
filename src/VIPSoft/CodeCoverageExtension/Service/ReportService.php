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
     * Constructor
     *
     * @param array  $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Generate report.
     *
     * @param \PHP_CodeCoverage $coverage
     */
    public function generateReport(\PHP_CodeCoverage $coverage)
    {
        $reportWriterClassName = $this->config['report']['class'];

        $writer = new $reportWriterClassName();
        $writer->process($coverage, $this->config['report']['directory']);
    }
}

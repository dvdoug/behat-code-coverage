<?php
/**
 * Code Coverage Report Service
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Service;

use VIPSoft\CodeCoverageCommon\Report\Factory;

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
     * @var \VIPSoft\CodeCoverageCommon\Report\Factory
     */
    private $factory;

    /**
     * Constructor
     *
     * @param array                                      $config
     * @param \VIPSoft\CodeCoverageCommon\Report\Factory $factory
     */
    public function __construct(array $config, Factory $factory)
    {
        $this->config  = $config;
        $this->factory = $factory;
    }

    /**
     * Generate report
     *
     * @param \PHP_CodeCoverage $coverage
     */
    public function generateReport(\PHP_CodeCoverage $coverage)
    {
        $format = $this->config['report']['format'];
        $options = $this->config['report']['options'];

        $report = $this->factory->create($format, $options);
        $report->process($coverage);
    }
}

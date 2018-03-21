<?php
/**
 * Code Coverage Report Service
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Service;

use LeanPHP\Behat\CodeCoverage\Common\Report\Factory;
use SebastianBergmann\CodeCoverage\CodeCoverage;

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
     * @var \LeanPHP\Behat\CodeCoverage\Common\Report\Factory
     */
    private $factory;

    /**
     * Constructor
     *
     * @param array                                             $config
     * @param \LeanPHP\Behat\CodeCoverage\Common\Report\Factory $factory
     */
    public function __construct(array $config, Factory $factory)
    {
        $this->config  = $config;
        $this->factory = $factory;
    }

    /**
     * Generate report
     *
     * @param CodeCoverage $coverage
     */
    public function generateReport(CodeCoverage $coverage)
    {
        $format = $this->config['report']['format'];
        $options = $this->config['report']['options'];

        $report = $this->factory->create($format, $options);
        $output = $report->process($coverage);

        if ('text' == $format) {
            print_r($output);
        }
    }
}

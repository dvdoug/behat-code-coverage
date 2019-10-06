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

use DVDoug\Behat\CodeCoverage\Common\Report\Factory;
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
     * @var \DVDoug\Behat\CodeCoverage\Common\Report\Factory
     */
    private $factory;

    /**
     * Constructor.
     */
    public function __construct(array $config, Factory $factory)
    {
        $this->config = $config;
        $this->factory = $factory;
    }

    /**
     * Generate report.
     */
    public function generateReport(CodeCoverage $coverage): void
    {
        $format = $this->config['report']['format'];
        $options = $this->config['report']['options'];

        $report = $this->factory->create($format, $options);
        $output = $report->process($coverage);

        if ('text' === $format) {
            print_r($output);
        }
    }
}

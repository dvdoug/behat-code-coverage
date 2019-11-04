<?php

declare(strict_types=1);
/**
 * Code Coverage XML Report.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-3-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Common\Report;

use DVDoug\Behat\CodeCoverage\Common\ReportInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade;

/**
 * XML report.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Xml implements ReportInterface
{
    /**
     * @var Facade
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
        if (!isset($options['target'])) {
            $options['target'] = null;
        }

        $this->report = new Facade('');
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CodeCoverage $coverage)
    {
        return $this->report->process(
            $coverage,
            $this->options['target']
        );
    }
}

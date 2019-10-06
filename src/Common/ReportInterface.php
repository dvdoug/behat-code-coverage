<?php

declare(strict_types=1);
/**
 * Report.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-3-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Common;

use SebastianBergmann\CodeCoverage\CodeCoverage;

/**
 * Report interface.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
interface ReportInterface
{
    /**
     * Constructor.
     */
    public function __construct(array $options);

    /**
     * Generate report.
     *
     * @return string|null
     */
    public function process(CodeCoverage $coverage);
}

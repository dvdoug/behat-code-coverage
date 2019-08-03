<?php

declare(strict_types=1);
/**
 * Code Coverage Report Factory.
 *
 * @copyright 2013 Anthon Pang
 *
 * @license BSD-3-Clause
 */

namespace DVDoug\Behat\CodeCoverage\Common\Report;

/**
 * Code coverage report factory.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Factory
{
    /**
     * Creation method.
     *
     * @param string $reportType
     * @param array  $options
     *
     * @return \DVDoug\Behat\CodeCoverage\Common\ReportInterface|null
     */
    public function create($reportType, array $options)
    {
        if (in_array($reportType, ['clover', 'crap4j', 'html', 'php', 'text', 'xml'])) {
            $className = '\DVDoug\Behat\CodeCoverage\Common\Report\\' . ucfirst($reportType);

            return new $className($options);
        }

        return null;
    }
}

<?php
/**
 * Code Coverage Report Factory
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-3-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

/**
 * Code coverage report factory
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Factory
{
    /**
     * Creation method
     *
     * @param string $reportType
     * @param array  $options
     *
     * @return \LeanPHP\Behat\CodeCoverage\Common\ReportInterface|null
     */
    public function create($reportType, array $options)
    {
        if (in_array($reportType, array('clover', 'crap4j', 'html', 'php', 'text', 'xml'))) {
            $className = '\LeanPHP\Behat\CodeCoverage\Common\Report\\' . ucfirst($reportType);

            return new $className($options);
        }

        return null;
    }
}

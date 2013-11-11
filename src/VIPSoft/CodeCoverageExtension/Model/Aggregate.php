<?php
/**
 * Aggregate
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Model;

/**
 * Aggregate
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class Aggregate
{
    /**
     * @var array
     */
    private $coverage;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->coverage = array();
    }

    /**
     * Update aggregated coverage
     *
     * @param string $class
     * @param array  $counts
     */
    public function update($class, $counts)
    {
        if ( ! isset($this->coverage[$class])) {
            $this->coverage[$class] = $counts;

            return;
        }

        foreach ($counts as $line => $status) {
            if ( ! isset($this->coverage[$class][$line]) || $status === 1) {
                $this->coverage[$class][$line] = $status;
            }
        }
    }

    /**
     * Get coverage
     *
     * @return array
     */
    public function getCoverage()
    {
        return $this->coverage;
    }
}

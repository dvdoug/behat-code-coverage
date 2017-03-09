<?php
/**
 * HTML Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Report\Factory;
use org\bovigo\vfs\vfsStream;

/**
 * HTML report test
 *
 * @group Unit
 */
class HtmlTest extends TestCase
{
    public function testProcess()
    {
        vfsStream::setup('tmp');
        $target = vfsStream::url('tmp');

        file_put_contents($target . '/file', "test\n");

        $report = new \PHP_CodeCoverage_Report_Node_Directory($target);
        $report->addFile('file', array('class' => array(1 => 1)), array(), false);

        $coverage = $this->getMock('PHP_CodeCoverage');
        $coverage->expects($this->once())
                 ->method('getReport')
                 ->will($this->returnValue($report));

        $report = new Html(array(
            'target' => $target,
        ));

        try {
            $result = $report->process($coverage);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            $this->fail();
        }
    }
}

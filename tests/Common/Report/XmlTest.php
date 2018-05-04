<?php
/**
 * XML Report
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Common\Report;

use VIPSoft\TestCase;
use LeanPHP\Behat\CodeCoverage\Common\Report\Factory;
use SebastianBergmann\CodeCoverage\Report\Xml\Facade;
use org\bovigo\vfs\vfsStream;

/**
 * XML report test
 *
 * @group Unit
 */
/**
 * TODO - reimplement integration tests'
class XmlTest extends TestCase
{
    /**
     * TODO - reimplement integration tests'
     *
    public function testProcess()
    {

        vfsStream::setup('tmp');
        $target = vfsStream::url('tmp');

        file_put_contents($target . '/file', "test\n");

        $report = new \SebastianBergmann\CodeCoverage\Node\Directory($target);
        $report->addFile('file', array('class' => array(1 => 1)), array(), false);

        $coverage = $this->createMock('SebastianBergmann\CodeCoverage\CodeCoverage');
        $coverage->expects($this->atLeast(1))
                 ->method('getReport')
                 ->will($this->returnValue($report));
        $coverage->expects($this->once())
                 ->method('getTests')
                 ->will($this->returnValue(array()));



        $report = new Xml(array(
            'target' => $target,
        ));

        try {
            $result = $report->process($coverage);
        } catch (\Exception $e) {
            echo 'aaaa';
            echo($e->getMessage());
            $this->fail();
        }
    }
}*/

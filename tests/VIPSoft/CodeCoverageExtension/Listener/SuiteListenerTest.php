<?php
/**
 * Extension
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace VIPSoft\CodeCoverageExtension\Listener;

use VIPSoft\TestCase;
use Behat\Behat\Event\SuiteEvent;

/**
 * @group Unit
 */
class SuiteListenerTest extends TestCase
{
    public function testGetSubscribedEvents()
    {
        $driver   = $this->getMock('PHP_CodeCoverage_Driver');
        $service  = $this->getMockBuilder('VIPSoft\CodeCoverageExtension\Service\ReportService')
                         ->disableOriginalConstructor()
                         ->getMock();
        $listener = new SuiteListener(array(), $driver, $driver, $service);
        $events   = $listener->getSubscribedEvents();

        $this->assertTrue(is_array($events));
        $this->assertEquals(2, count($events));
        $this->assertEquals('beforeSuite', $events['beforeSuite']);
        $this->assertEquals('afterSuite', $events['afterSuite']);
    }

    public function testBeforeSuite()
    {
        $event = $this->getMockBuilder('Behat\Behat\Event\SuiteEvent')
                      ->disableOriginalConstructor()
                      ->getMock();

        $driver = $this->getMock('PHP_CodeCoverage_Driver');
        $driver->expects($this->once())
               ->method('start');

        $service  = $this->getMockBuilder('VIPSoft\CodeCoverageExtension\Service\ReportService')
                         ->disableOriginalConstructor()
                         ->getMock();

        $listener = new SuiteListener(array('drivers' => array('remote')), $driver, $driver, $service);
        $listener->beforeSuite($event);
    }

    public function testAfterSuite()
    {
        $event = $this->getMockBuilder('Behat\Behat\Event\SuiteEvent')
                      ->disableOriginalConstructor()
                      ->getMock();

        $driver = $this->getMock('PHP_CodeCoverage_Driver');
        $driver->expects($this->once())
               ->method('stop')
               ->will($this->returnValue(array('dummy' => array(1 => 1))));

        $service  = $this->getMockBuilder('VIPSoft\CodeCoverageExtension\Service\ReportService')
                         ->disableOriginalConstructor()
                         ->getMock();

        $listener = new SuiteListener(array('drivers' => array('local')), $driver, $driver, $service);
        $listener->afterSuite($event);
    }
}

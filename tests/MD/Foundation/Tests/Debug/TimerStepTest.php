<?php
namespace MD\Foundation\Tests\Debug;

use MD\Foundation\Debug\TimerStep;

class TimerStepTest extends \PHPUnit_Framework_TestCase
{

    protected function _provideStep($instantStart = true) {
        return new TimerStep($instantStart, TimerStep::getMicroTime(), memory_get_usage(true), memory_get_peak_usage(true));
    }

    public function testInstantStart() {
        $timerStep = $this->_provideStep(true);

        $this->assertAttributeInternalType('float', '_startTime', $timerStep);
        $this->assertAttributeGreaterThan(0, '_startTime', $timerStep);

        $this->assertAttributeInternalType('integer', '_startMemory', $timerStep);
        $this->assertAttributeGreaterThan(0, '_startMemory', $timerStep);

        $this->assertAttributeInternalType('integer', '_startMemoryPeak', $timerStep);
        $this->assertAttributeGreaterThan(0, '_startMemoryPeak', $timerStep);
    }

    public function testInstantStartOff() {
        $timerStep = $this->_provideStep(false);

        $this->assertAttributeEquals(null, '_startTime', $timerStep);
        $this->assertAttributeEquals(null, '_startMemory', $timerStep);
        $this->assertAttributeEquals(null, '_startMemoryPeak', $timerStep);
    }

    public function testStart() {
        $timerStep = $this->_provideStep(false);
        $timerStep->start();

        $this->assertAttributeInternalType('float', '_startTime', $timerStep);
        $this->assertAttributeGreaterThan(0, '_startTime', $timerStep);

        $this->assertAttributeInternalType('integer', '_startMemory', $timerStep);
        $this->assertAttributeGreaterThan(0, '_startMemory', $timerStep);

        $this->assertAttributeInternalType('integer', '_startMemoryPeak', $timerStep);
        $this->assertAttributeGreaterThan(0, '_startMemoryPeak', $timerStep);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testDoubleStart() {
        $timerStep = $this->_provideStep(true);
        $timerStep->start();
    }

    public function testStop() {
        $timerStep = $this->_provideStep(true);
        $duration = $timerStep->stop();
        $this->assertInternalType('float', $duration);
        $this->assertTrue($duration > 0);
    }

    public function testStopPrecision() {
        // test default
        $timerStep = $this->_provideStep(true);
        $duration = explode('.', $timerStep->stop());
        $this->assertEquals(8, strlen(end($duration)));

        // now test custom
        foreach(array(0, 1, 2, 5, 8, 10, 20) as $precision) {
           $timerStep = $this->_provideStep(true);
            $duration = explode('.', $timerStep->stop($precision));
            if (count($duration) === 2) {
                $this->assertTrue(strlen(end($duration)) <= $precision);
            }
        }
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testDoubleStop() {
        $timerStep = $this->_provideStep(true);
        $timerStep->stop();
        $timerStep->stop();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testStep() {
        $timerStep = $this->_provideStep();
        $timerStep->step();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetStep() {
        $timerStep = $this->_provideStep();
        $timerStep->getSteps();
    }

}

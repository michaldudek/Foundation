<?php
namespace MD\Foundation\Tests\Debug;

use MD\Foundation\Debug\Timer;
use MD\Foundation\Debug\TimerStep;
use MD\Foundation\Utils\StringUtils;

class TimerTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantStart() {
        $timer = new Timer();

        $this->assertAttributeInternalType('float', '_startTime', $timer);
        $this->assertAttributeGreaterThan(0, '_startTime', $timer);

        $this->assertAttributeInternalType('integer', '_startMemory', $timer);
        $this->assertAttributeGreaterThan(0, '_startMemory', $timer);

        $this->assertAttributeInternalType('integer', '_startMemoryPeak', $timer);
        $this->assertAttributeGreaterThan(0, '_startMemoryPeak', $timer);

        $this->assertAttributeInstanceOf('MD\Foundation\Debug\TimerStep', '_currentStep', $timer);
    }

    public function testInstantStartOff() {
        $timer = new Timer(false);

        $this->assertAttributeEquals(null, '_startTime', $timer);
        $this->assertAttributeEquals(null, '_startMemory', $timer);
        $this->assertAttributeEquals(null, '_startMemoryPeak', $timer);
        $this->assertAttributeEquals(null, '_currentStep', $timer);
    }

    public function testStart() {
        $timer = new Timer(false);
        $timer->start();

        $this->assertAttributeInternalType('float', '_startTime', $timer);
        $this->assertAttributeGreaterThan(0, '_startTime', $timer);

        $this->assertAttributeInternalType('integer', '_startMemory', $timer);
        $this->assertAttributeGreaterThan(0, '_startMemory', $timer);

        $this->assertAttributeInternalType('integer', '_startMemoryPeak', $timer);
        $this->assertAttributeGreaterThan(0, '_startMemoryPeak', $timer);

        $this->assertAttributeInstanceOf('MD\Foundation\Debug\TimerStep', '_currentStep', $timer);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testDoubleStart() {
        $timer = new Timer();
        $timer->start();
    }

    public function testStop() {
        $timer = new Timer();
        $duration = $timer->stop();
        $this->assertInternalType('float', $duration);
        $this->assertTrue($duration > 0);
    }

    public function testStopPrecision() {
        // test default
        $timer = new Timer();
        $duration = explode('.', $timer->stop());
        $this->assertTrue(strlen(end($duration)) <= 8);

        // now test custom
        foreach(array(0, 1, 2, 5, 8, 10, 20) as $precision) {
            $timer = new Timer();
            $duration = explode('.', $timer->stop($precision));
            if (count($duration) === 2) {
                $this->assertTrue(strlen(end($duration)) <= $precision);
            }
        }
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testDoubleStop() {
        $timer = new Timer();
        $timer->stop();
        $timer->stop();
    }

    public function testStep() {
        $timer = new Timer();
        $step = $timer->getCurrentStep();
        $firstStep = $timer->step();
        $this->assertEquals($step, $firstStep);
        $this->assertNotEquals($firstStep, $timer->getCurrentStep());
    }

    public function testStepNames() {
        $stepNames = array(
            'First Step',
            'Second Step',
            '3'
        );

        $timer = new Timer();
        $steps = array();
        $steps[] = $timer->step($stepNames[0]);
        $steps[] = $timer->step($stepNames[1]);
        $steps[] = $timer->step();

        foreach($timer->getSteps() as $i => $step) {
            $this->assertArrayHasKey('name', $step);
            $this->assertArrayHasKey('step', $step);
            
            $this->assertEquals($steps[$i], $step['step']);
            $this->assertEquals($stepNames[$i], $step['name']);
        }
    }

    public function testGetDuration() {
        $timer = new Timer();
        $duration = $timer->getDuration();
        $this->assertInternalType('float', $duration);
        $this->assertTrue($duration > 0);
        $this->assertTrue($duration < 1);
    }

    public function testGetDurationNotStoppingTimer() {
        $timer = new Timer();
        $duration = $timer->getDuration();
        $this->assertNull($timer->getStopTime());
    }

    public function testGetMemoryUsage() {
        $timer = new Timer();

        // fill a lot of memory
        $array = array();
        for($i = 0; $i < 99999; $i++) {
            $array[] = StringUtils::random(32);
        }

        $memory = $timer->getMemoryUsage();
        $this->assertInternalType('int', $memory);
        $this->assertGreaterThan(0, $memory);
    }

    public function testGetters() {
        $timer = new Timer();
        $this->assertAttributeEquals($timer->getStartTime(), '_startTime', $timer);
        $this->assertAttributeEquals($timer->getStartMemory(), '_startMemory', $timer);
        $this->assertAttributeEquals($timer->getStartMemoryPeak(), '_startMemoryPeak', $timer);
        $this->assertAttributeEquals($timer->getCurrentStep(), '_currentStep', $timer);

        $timer->stop();

        $this->assertAttributeEquals($timer->getStopTime(), '_stopTime', $timer);
        $this->assertAttributeEquals($timer->getStopMemory(), '_stopMemory', $timer);
        $this->assertAttributeEquals($timer->getStopMemoryPeak(), '_stopMemoryPeak', $timer);
    }

    public function testGetSteps() {
        $timer = new Timer();

        $this->assertAttributeEquals($timer->getSteps(), '_steps', $timer);

        $this->assertInternalType('array', $timer->getSteps());
        $this->assertEmpty($timer->getSteps());

        $timer->step();
        $this->assertNotEmpty($timer->getSteps());

        $timer->step();
        $timer->step();

        $this->assertAttributeEquals($timer->getSteps(), '_steps', $timer);

        $this->assertEquals(3, count($timer->getSteps()));

        foreach($timer->getSteps() as $step) {
            $this->assertArrayHasKey('step', $step);
            $this->assertArrayHasKey('name', $step);
        }
    }

    public function testDifference() {
        $start = Timer::getMicroTime();
        $diff = 0.04525;
        $epsilon = 0.0001;
        $end = $start + $diff;
        $this->assertInternalType('float', Timer::difference($start, $end));
        $this->assertTrue(abs(Timer::difference($start, $end) - $diff) < $epsilon);

        // test precision
        foreach(array(0, 1, 2, 5, 8, 10, 20) as $precision) {
            $difference = Timer::difference($start, $end, $precision);
            $diffArray = explode('.', strval($difference));
            if (count($diffArray) === 2) {
                $this->assertTrue(strlen(end($diffArray)) <= $precision, 'Failed asserting proper precision length for precision of '. $precision .' ('. $difference .' given).');
            }
        }
    }

    public function testMemoryDifference() {
        $start = 1024 * 4.5;
        $this->assertEquals(1024 * 4.3, Timer::memoryDifference($start, $start + 1024 * 4.3));
    }

    public function testGetMicroTime() {
        $this->assertInternalType('float', Timer::getMicroTime());
        $now = time();
        $mtime = Timer::getMicroTime();
        $this->assertTrue($mtime > $now);
        $this->assertTrue($now + 1 > $mtime);
    }

    public function testGetCurrentMemory() {
        $current = memory_get_usage(true);
        $fromTimer = Timer::getCurrentMemory();

        $this->assertInternalType('int', $fromTimer);

        // make sure diff isn't bigger than 8kb
        // because memory actually can be different when timer reads it
        // and when the test reads it
        $diff = abs($current - $fromTimer);
        $this->assertLessThanOrEqual(1024 * 8, $diff);
    }

    public function testGetCurrentMemoryPeak() {
        $this->assertEquals(memory_get_peak_usage(true), Timer::getCurrentMemoryPeak());
        $this->assertInternalType('int', Timer::getCurrentMemoryPeak());
        $this->assertTrue(Timer::getCurrentMemoryPeak() > 0);
    }

}

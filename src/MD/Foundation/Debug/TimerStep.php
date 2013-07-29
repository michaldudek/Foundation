<?php
/**
 * Timer step to be used as a step of a parent timer.
 * 
 * @package Foundation
 * @subpackage Debug
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation\Debug;

use BadMethodCallException;

use MD\Foundation\Debug\Timer;

class TimerStep extends Timer
{

    /**
     * Constructor.
     * 
     * Starts the timer automatically by default.
     * 
     * @param bool $instantStart [optional] Should the timer be started immediately? Default: true.
     * @param float $startTime [optional] Start time (passed here so it's the same as the previous step, to not lose any microsecond).
     * @param float $startMemory [optional] Start memory usage (passed here so it's the same as the previous step, to not lose any byte).
     * @param float $startMemoryPeak [optional] Start memory peak (passed here so it's the same as the previous step, to not lose any byte).
     */
    public function __construct($instantStart = true, $startTime = null, $startMemory = null, $startMemoryPeak = null) {
        if ($instantStart) {
            $this->start($startTime, $startMemory, $startMemoryPeak);
        }
    }
    
    /**
     * Start the timer step.
     * 
     * @throws BadMethodCallException When the timer step was already started.
     */
    public function start($startTime = null, $startMemory = null, $startMemoryPeak = null) {
        if ($this->_startTime) {
            throw new BadMethodCallException('Timer step already started.');
        }

        $this->_startTime = isset($startTime) ? $startTime : static::getMicroTime();
        $this->_startMemory = isset($startMemory) ? $startMemory : static::getCurrentMemory();
        $this->_startMemoryPeak = isset($startMemoryPeak) ? $startMemoryPeak : static::getCurrentMemoryPeak();
    }
    
    /**
     * Stop the timer and return the result.
     * 
     * @param int $precision [optional] How many places to round to. Default: 8
     * @return float Duration in seconds.
     * 
     * @throws BadMethodCallException When the timer step was already stopped.
     */
    public function stop($precision = 8) {
        if ($this->_stopTime) {
            throw new BadMethodCallException('Timer already stopped.');
        }

        $this->_stopTime = static::getMicroTime();
        $this->_stopMemory = static::getCurrentMemory();
        $this->_stopMemoryPeak = static::getCurrentMemoryPeak();

        return static::difference($this->_startTime, $this->_stopTime, $precision);
    }

    /**
     * Timer step cannot track any steps.
     * 
     * @throws BadMethodCallException Always thrown when this method is called.
     */
    public function step($name = null) {
        throw new BadMethodCallException('Timer step cannot track any steps.');
    }

    /*****************************************************
     * SETTERS AND GETTERS
     *****************************************************/
    /**
     * Timer step cannot track any steps.
     * 
     * @throws BadMethodCallException Always thrown when this method is called.
     */
    public function getSteps() {
        throw new BadMethodCallException('Timer step cannot track any steps.');
    }
    
}
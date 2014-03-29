<?php
/**
 * Helper class to use with Psr\Log that helps on evaluating log levels hierarchy.
 * 
 * @package Foundation
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation;

use Psr\Log\LogLevel;

use MD\Foundation\Exceptions\InvalidArgumentException;

class LogLevels
{

    const EMERGENCY = 600;
    const ALERT = 550;
    const CRITICAL = 500;
    const ERROR = 400;
    const WARNING = 300;
    const NOTICE = 250;
    const INFO = 200;
    const DEBUG = 100;

    /**
     * Map of log levels and their numeric values.
     * 
     * @var array
     */
    protected static $levels = array(
        LogLevel::EMERGENCY => self::EMERGENCY,
        LogLevel::ALERT => self::ALERT,
        LogLevel::CRITICAL => self::CRITICAL,
        LogLevel::ERROR => self::ERROR,
        LogLevel::WARNING => self::WARNING,
        LogLevel::NOTICE => self::NOTICE,
        LogLevel::INFO => self::INFO,
        LogLevel::DEBUG => self::DEBUG
    );

    /**
     * Evaluates the given PSR log level as an integer.
     * 
     * @param  string $level One of Psr\Log\LogLevel constants.
     * @return int
     *
     * @throws InvalidArgumentException When unrecognized log level.
     */
    public static function evaluateLevel($level) {
        if (!isset(static::$levels[$level])) {
            throw new InvalidArgumentException('one of \Psr\Log\LogLevel constants', $level);
        }

        return static::$levels[$level];
    }

    /**
     * Checks if the given level is higher than the other one.
     * 
     * @param  string  $level   Level to be compared. One of Psr\Log\LogLevel constants.
     * @param  string  $against Level to be compared against. One of Psr\Log\LogLevel constants.
     * @param  boolean $inclusive [optional] Should the comparison be inclusive, ie. higher than or equal. Default: false.
     * @return boolean
     */
    public static function isHigherLevel($level, $against, $inclusive = false) {
        return $inclusive
            ? static::evaluateLevel($level) >= static::evaluateLevel($against)
            : static::evaluateLevel($level) > static::evaluateLevel($against);
    }

    /**
     * Checks if the given level is lower than the other one.
     * 
     * @param  string  $level   Level to be compared. One of Psr\Log\LogLevel constants.
     * @param  string  $against Level to be compared against. One of Psr\Log\LogLevel constants.
     * @param  boolean $inclusive [optional] Should the comparison be inclusive, ie. lower than or equal. Default: false.
     * @return boolean
     */
    public static function isLowerLevel($level, $against, $inclusive = false) {
        return $inclusive
            ? static::evaluateLevel($level) <= static::evaluateLevel($against)
            : static::evaluateLevel($level) < static::evaluateLevel($against);
    }

}
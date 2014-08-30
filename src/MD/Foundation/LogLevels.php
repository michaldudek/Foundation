<?php
/**
 * @package Foundation
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation;

use Psr\Log\LogLevel;

use MD\Foundation\Exceptions\InvalidArgumentException;

/**
 * Helper class to use with `\Psr\Log` that helps in evaluating log levels hierarchy.
 *
 * Example use case:
 *
 *      use MD\Foundation\LogLevels;
 *      use Psr\Log\LogLevel;
 *      use Psr\Log\LoggerInterface;
 *
 *      class SMSLogger implements LoggerInterface
 *      {
 *          public function log($level, $message, array $context) {
 *              if (LogLevels::isHigherLevel($level, LogLevel::ALERT, true)) {
 *                  $this->sendSms($message, $context);
 *              }
 *          }
 *      }
 *
 * It is useful when you want to filter out some log messages based on their importance,
 * as PSR-3 doesn't really define the hierarchy here.
 */
class LogLevels
{

    /** 600 */
    const EMERGENCY = 600;
    /** 550 */
    const ALERT = 550;
    /** 500 */
    const CRITICAL = 500;
    /** 400 */
    const ERROR = 400;
    /** 300 */
    const WARNING = 300;
    /** 250 */
    const NOTICE = 250;
    /** 200 */
    const INFO = 200;
    /** 100 */
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
     * Example:
     * 
     *      echo \MD\Foundation\LogLevels::evaluateLevel(\Psr\Log\LogLevel::CRITICAL);
     *      // -> 500
     * 
     * @param  string $level One of `\Psr\Log\LogLevel` constants.
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
     * Example:
     *
     *      echo \MD\Foundation\LogLevels::isHigherLevel(\Psr\Log\LogLevel::CRITICAL, \PSR\Log\LogLevel::WARNING);
     *      // -> true
     * 
     * @param  string  $level   Level to be compared. One of `\Psr\Log\LogLevel` constants.
     * @param  string  $against Level to be compared against. One of `\Psr\Log\LogLevel` constants.
     * @param  boolean $inclusive [optional] Should the comparison be inclusive, ie. higher than or equal. Default: `false`.
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
     * Example:
     *
     *      echo \MD\Foundation\LogLevels::isLowerLevel(\Psr\Log\LogLevel::CRITICAL, \PSR\Log\LogLevel::WARNING);
     *      // -> false
     * 
     * @param  string  $level   Level to be compared.  One of `\Psr\Log\LogLevel` constants.
     * @param  string  $against Level to be compared against.  One of `\Psr\Log\LogLevel` constants.
     * @param  boolean $inclusive [optional] Should the comparison be inclusive, ie. lower than or equal. Default: `false`.
     * @return boolean
     */
    public static function isLowerLevel($level, $against, $inclusive = false) {
        return $inclusive
            ? static::evaluateLevel($level) <= static::evaluateLevel($against)
            : static::evaluateLevel($level) < static::evaluateLevel($against);
    }

}
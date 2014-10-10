<?php
/**
 * @package Foundation
 * @subpackage Exceptions
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation\Exceptions;

use InvalidArgumentException as Base_InvalidArgumentException;

use MD\Foundation\Debug\Debugger;
use MD\Foundation\Utils\StringUtils;

/**
 * Invalid argument exception that easily generates the exception message.
 *
 * It will inspect the exception stack trace and automatically set the exception message
 * to include the function name that threw the exception.
 *
 * Example:
 *
 *      function myFunction($str) {
 *          if (empty($str)) {
 *              throw new \MD\Foundation\Exceptions\InvalidArgumentException('non-empty string', $str);
 *          }
 *      }
 *
 *      try {
 *          myFunction('');
 *      } catch(\InvalidArgumentException $e) {
 *          echo $e->getMessage();
 *          // -> myFunction expected argument 1 to be non-empty string, string ("") given.
 *      }
 */
class InvalidArgumentException extends Base_InvalidArgumentException
{

    /**
     * Constructor.
     * 
     * @param string $expected Expected type.
     * @param mixed $actual Actual argument given.
     * @param int $number [optional] Argument number.
     * @param bool $hideCaller [optional] Should the function that has thrown this exception be hidden? Default: `false`.
     */
    public function __construct($expected, $actual, $number = 1, $hideCaller = false) {
        $trace = Debugger::getPrettyTrace(debug_backtrace());
        $type = Debugger::getType($actual);

        $type = $type === 'string' ? $type .' ("'. StringUtils::truncate($actual, 50) .'")' : $type;

        if (!$hideCaller && isset($trace[1])) {
            $message = $trace[1]['function'] .' expected argument '. $number .' to be '. $expected .', '. $type .' given.';
        } else {
            $message = 'Expected argument '. $number .' to be '. $expected .', '. $type .' given.';
        }

        parent::__construct($message);
    }

}

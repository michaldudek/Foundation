<?php
/**
 * Invalid argument exception that easily generates the exception message.
 * 
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

class InvalidArgumentException extends Base_InvalidArgumentException
{

    /**
     * Constructor.
     * 
     * @param string $expected Expected type.
     * @param mixed $actual Actual argument given.
     * @param int $number [optional] Argument number.
     * @param bool $hideCaller [optional] Should the function that has thrown this exception be hidden? Default: false.
     */
    public function __construct($expected, $actual, $number = 1, $hideCaller = false) {
        $trace = Debugger::getPrettyTrace(debug_backtrace());
        $type = Debugger::getType($actual);

        if (!$hideCaller && isset($trace[1])) {
            $message = $trace[1]['function'] .' expected argument '. $number .' to be '. $expected .', '. $type .' given.';
        } else {
            $message = 'Expected argument '. $number .' to be '. $expected .', '. $type .' given.';
        }

        parent::__construct($message);
    }

}
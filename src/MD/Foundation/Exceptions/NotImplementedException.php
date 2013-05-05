<?php
/**
 * Throw this exception for convenience if you want to mark
 * a function or method or functionality that has not been implemented yet.
 * 
 * @package Foundation
 * @subpackage Exceptions
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation\Exceptions;

use Exception;
use RuntimeException;

use MD\Foundation\Debug\Debugger;

class NotImplementedException extends RuntimeException
{

    public function __construct($message = '', $code = 0, Exception $previous = null) {
        if (empty($message)) {
            $trace = Debugger::getPrettyTrace(debug_backtrace());
            if (isset($trace[1])) {
                $message = 'Function "'. $trace[1]['function'] .'" defined in "'. $trace[1]['file'] .'" has not been fully implemented yet.';
            }
        }

        parent::__construct($message, $code, $previous);
    }

}
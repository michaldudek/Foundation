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

use Exception as Base_Exception;
use RuntimeException;

use MD\Foundation\Debug\Debugger;

/**
 * Exception thrown when something is not yet implemented.
 * 
 * Throw this exception for convenience if you want to mark a function or method
 * or functionality that has not been implemented yet.
 *
 * Alternative to putting `@todo` comments around in the code and giving them more visibility :)
 *
 * Example:
 *
 *      function doSomething() {
 *          throw new \MD\Foundation\Exceptions\NotImplementedException();
 *      }
 *
 *      doSomething()
 *      // -> Uncaught exception: Function "doSomething" defined in "example.php" has not been fully implemented yet.
 */
class NotImplementedException extends RuntimeException
{

    /**
     * Constructor.
     * 
     * @param string  $message  [optional] Exception message. Leave empty to autogenerate a message. Default: `""`.
     * @param integer $code     [optional] Exception code. Default: `0`.
     * @param Exception $previous [optional] Previous exception. Default: `null`.
     */
    public function __construct($message = '', $code = 0, Base_Exception $previous = null) {
        if (empty($message)) {
            $trace = Debugger::getPrettyTrace(debug_backtrace());
            if (isset($trace[1])) {
                $message = 'Function "'. $trace[1]['function'] .'" defined in "'. $trace[1]['file'] .'" has not been fully implemented yet.';
            }
        }

        parent::__construct($message, $code, $previous);
    }

}

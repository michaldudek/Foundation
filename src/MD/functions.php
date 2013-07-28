<?php
/**
 * Few global functions that Foundation registers for ease of use.
 * 
 * @package Foundation
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD;

use MD\Foundation\Debug\Debugger;

/**
 * Friendly output of variables. It will print them out in <pre class="md-dump"> tag.
 * 
 * It will dump all arguments sent to this function.
 * 
 * @param mixed $variable1 Variable to be dumped.
 * @param mixed $variable2 Another variable to be dumped.
 * @param mixed $variable3 Another variable to be dumped.
 * @param ...
 * 
 * @codeCoverageIgnore
 */
function dump() {
    call_user_func_array(array('MD\Foundation\Debug\Debugger', 'dump'), func_get_args());
}

/**
 * Returns a string containing HTML formatted information about the passed variable.
 * 
 * @param mixed $variable1 Variable to be dumped.
 * @param mixed $variable2 Another variable to be dumped.
 * @param mixed $variable3 Another variable to be dumped.
 * @param ...
 * @return string
 * 
 * @codeCoverageIgnore
 */
function string_dump() {
    return call_user_func_array(array('MD\Foundation\Debug\Debugger', 'stringDump'), func_get_args());
}

/**
 * Dumps all the arguments into browser's JavaScript console.
 * 
 * Attempts to convert all objects into arrays.
 * 
 * @param mixed $variable1 Variable to be dumped.
 * @param mixed $variable2 Another variable to be dumped.
 * @param mixed $variable3 Another variable to be dumped.
 * @param ...
 * 
 * @codeCoverageIgnore
 */
function console_dump() {
    call_user_func_array(array('MD\Foundation\Debug\Debugger', 'consoleDump'), func_get_args());
}

/**
 * Dumps all the arguments into browser's JavaScript console.
 * 
 * Attempts to convert all objects into arrays.
 * 
 * @param mixed $variable1 Variable to be dumped.
 * @param mixed $variable2 Another variable to be dumped.
 * @param mixed $variable3 Another variable to be dumped.
 * @param ...
 * 
 * @codeCoverageIgnore
 */
function console_log() {
    call_user_func_array(array('MD\Foundation\Debug\Debugger', 'consoleDump'), func_get_args());
}

/**
 * Returns a string containing JavaScript code that will log all the arguments into browser's JavaScript console.
 * 
 * Attempts to convert all objects into arrays.
 * 
 * @param mixed $variable1 Variable to be dumped.
 * @param mixed $variable2 Another variable to be dumped.
 * @param mixed $variable3 Another variable to be dumped.
 * @param ...
 * @return string
 * 
 * @codeCoverageIgnore
 */
function console_string_dump() {
    return call_user_func_array(array('MD\Foundation\Debug\Debugger', 'consoleStringDump'), func_get_args());
}
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
 */
function console_string_dump() {
    return call_user_func_array(array('MD\Foundation\Debug\Debugger', 'consoleStringDump'), func_get_args());
}


// a fix for apache_request_headers() if it's not available

if(!function_exists('apache_request_headers')) {
    /**
     * Returns request headers.
     * 
     * @return array
     */
    /*
    function apache_request_headers() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if(substr($key, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }
    */
}
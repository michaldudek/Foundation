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

use Splot\Foundation\Debug\Debugger;

/**
 * Friendly output of variables. It will print them out in <pre class="md-dump"> tag.
 * 
 * @param object $variable Variable to be dumped.
 * @param bool $toString [optional] Should return a string instead of echoing the output? Default: false.
 * @return string
 */
function dump($variable, $toString = false) {
    return Debugger::dump($variable, $toString);
}


// a fix for apache_request_headers() if it's not available
if(!function_exists('apache_request_headers')) {
    /**
     * Returns request headers.
     * 
     * @return array
     */
    function apache_request_headers() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if(substr($key, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
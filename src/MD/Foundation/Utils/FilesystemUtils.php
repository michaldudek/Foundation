<?php
/**
 * A set of filesystem utility functions.
 * 
 * @package Foundation
 * @subpackage Utils
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation\Utils;

use MD\Foundation\Utils\ArrayUtils;

/**
 * @static
 */
class FilesystemUtils
{

    const GLOB_ROOTFIRST = 1024;
    const GLOB_CHILDFIRST =  2048;
    
    /**
     * Extended glob() functionality that supports double star "**" wildcard.
     *
     * PHP's glob() implementation doesn't allow for "**" wildcard. In Bash 4 it can be enabled with "globstar" setting.
     *
     * In case the "**" wildcard is not used in the pattern then this method just calls PHP's glob().
     *
     * For full documentation see PHP's glob() documentation.
     * 
     * @param  string  $pattern The pattern. Supports "**" wildcard.
     * @param  integer $flags [optional] glob() flags. See glob()'s documentation. Default: 0.
     * @return array|boolean
     */
    public static function glob($pattern, $flags = 0) {
        // if not using ** then just use PHP's glob()
        if (stripos($pattern, '**') === false) {
            // turn off the custom flags
            $files = glob($pattern, ($flags | static::GLOB_CHILDFIRST | static::GLOB_ROOTFIRST) ^ (static::GLOB_CHILDFIRST | static::GLOB_ROOTFIRST));

            // sort alphabetically
            sort($files);

            // sort by root first?
            if ($flags & static::GLOB_ROOTFIRST) {
                $files = ArrayUtils::sortPaths($files, true);
            } else if ($flags & static::GLOB_CHILDFIRST) {
                $files = ArrayUtils::sortPaths($files, false);
            }

            return $files;
        }

        $patterns = array();

        // if globstar is inside braces
        if ($flags & GLOB_BRACE) {
            $regexp = '/\{(.+)?([\*]{2}[^,]?)(.?)\}/i';
            // check if this situation really occurs (otherwise we can end up with infinite nesting)
            if (preg_match($regexp, $pattern)) {
                // extract the globstar from inside the braces and add a new pattern to patterns list
                $patterns[] = preg_replace_callback('/(.+)?\{(.+)?([\*]{2}[^,]?)(.?)\}(.?)/i', function($matches) {
                    $brace = '{'. $matches[2] . $matches[4] .'}';
                    if ($brace === '{,}' || $brace === '{}') {
                        $brace = '';
                    }

                    $pattern = $matches[1] . $brace . $matches[5];
                    return str_replace('//', '/', $pattern);
                }, $pattern);

                // and now change the braces in the main pattern to globstar
                $pattern = preg_replace_callback($regexp, function($matches) {
                    return $matches[2];
                }, $pattern);
            }
        }

        $files = array();

        $pos = stripos($pattern, '**');

        $rootPattern = substr($pattern, 0, $pos) .'*';
        $restPattern = substr($pattern, $pos + 2);

        while($dirs = glob($rootPattern, GLOB_ONLYDIR)) {
            $rootPattern = $rootPattern .'/*';

            foreach($dirs as $dir) {
                $patterns[] = $dir . $restPattern;
            }
        }

        foreach($patterns as $pat) {
            $files = array_merge($files, static::glob($pat, $flags));
        }

        $files = array_unique($files);

        // sort alphabetically
        sort($files);

        // sort by root first?
        if ($flags & static::GLOB_ROOTFIRST) {
            $files = ArrayUtils::sortPaths($files, true);
        } else if ($flags & static::GLOB_CHILDFIRST) {
            $files = ArrayUtils::sortPaths($files, false);
        }

        return $files;
    }
    
}

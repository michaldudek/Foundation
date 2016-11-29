<?php
/**
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
 * A set of filesystem utility functions.
 */
class FilesystemUtils
{

    const GLOB_ROOTFIRST = 32768;
    const GLOB_CHILDFIRST =  65536;
    
    /**
     * Extended `glob()` functionality that supports double star `**` (globstar) wildcard.
     *
     * PHP's `glob()` implementation doesn't allow for `**` wildcard. In Bash 4 it can be enabled with `globstar` setting.
     *
     * In case the `**` wildcard is not used in the pattern then this method just calls PHP's `glob()`.
     *
     * For full documentation see PHP's [`glob()` documentation](http://php.net/manual/en/function.glob.php).
     *
     * It's worth noting that if you want to find files inside current directory and their subdirectories,
     * then you have to use a `GLOB_BRACE` flag and pattern, e.g.:
     * 
     *     echo \MD\Foundation\Utils\FilesystemUtils::glob('{,** /}*.js', GLOB_BRACE); // note: remove space between * and /
     *     // -> array(
     *     //      'main.js',
     *     //      'dir/script.js',
     *     //      'dir/more/scripts.js'
     *     // );
     *      
     * Implementation of this convention varies between libs in various languages and `MD\Foundation` sticks
     * with what [Bash manual states](http://www.gnu.org/software/bash/manual/bashref.html#Pattern-Matching).
     * More about this is explained in [#2](https://github.com/michaldudek/Foundation/issues/2).
     *
     * This function also supports its own implementation of `GLOB_BRACE` flag on systems that do not support it
     * (e.g. Alpine Linux, popular base for Docker containers). Because it's impossible to detect if that flag was
     * passed or not (as it has `null` or `0` value), on such systems the function assumes that yes, this flag was
     * passed if there are any `{}` braces used in the pattern. This implementation might not be so fast as a system
     * implementation, so use with caution or switch to "fuller" distro.
     *
     * Additionally it provides sorting option to the results, which you can pass along with
     * other flags. Constants `FilesystemUtils::GLOB_ROOTFIRST` and `FilesystemUtils::GLOB_CHILDFIRST`
     * sort the results either as "root first" where files in a directory are listed before directories and
     * subdirectories, or "child first" where subdirectories are listed before files.
     * 
     * @param  string  $pattern The pattern. Supports `**` wildcard.
     * @param  int $flags [optional] `glob()` flags. See `glob()`'s documentation. Default: `0`.
     * @return array|bool
     */
    public static function glob($pattern, $flags = 0) {
        // turn off custom flags
        $globFlags = ($flags | static::GLOB_CHILDFIRST | static::GLOB_ROOTFIRST) ^ (static::GLOB_CHILDFIRST | static::GLOB_ROOTFIRST);

        // our custom implementation will be expanding some patterns (namely globstar and braces) so we gather them all
        $patterns = array($pattern);

        // expand GLOB_BRACE if it's not defined on the system (e.g. Alpine Linux)
        // let's assume that it's passed always in such cases (most common usage)
        // and keeping the original pattern will make us safe to detect the desired files with {} in name anyway
        if (!defined('GLOB_BRACE') || !GLOB_BRACE) {
            $patterns = array_merge(
                $patterns,
                self::globBraceExpand($patterns)
            );
        }

        // expand globstar if added
        if (stripos($pattern, '**') !== false) {
            $patterns = array_merge(
                $patterns,
                self::globStarExpand($patterns, $globFlags)
            );
        }

        // finally when all patterns expanded, just rerun them and merge results
        $files = array();
        foreach($patterns as $pat) {
            $files = array_merge($files, glob($pat, $globFlags));
        }

        // fix some paths as they might have gotten double // due to not-perfect pattern expansion
        $files = array_map(function($file) {
            return str_replace('//', '/', $file);
        }, $files);

        // make sure no repetitions from all the patterns provided
        $files = array_unique($files);

        // sort by root first?
        if ($flags & static::GLOB_ROOTFIRST) {
            $files = ArrayUtils::sortPaths($files, true);
        } else if ($flags & static::GLOB_CHILDFIRST) {
            $files = ArrayUtils::sortPaths($files, false);
        } elseif (!($flags & GLOB_NOSORT)) {
            // default sort order is alphabetically
            sort($files);
        }

        return $files;
    }

    /**
     * Helper method that assists `::glob()` in handling `GLOB_BRACE` flag on systems that do not support it,
     * e.g. Alpine Linux.
     *
     * It expands the passed list of patterns into more patterns without the braces.
     *
     * @param  array|string $patterns An array of glob patterns or a single pattern.
     * @return array
     */
    private static function globBraceExpand($patterns) {
        $patterns = is_array($patterns) ? $patterns : array($patterns);
        $results = array();

        $braceRegexp = '/\{([^\}]+)\}/i';

        // we need to expand the patterns recursively in case there are few groups of braces
        // therefore let's define an "expander" function
        $expander = function($pattern, $self) use (&$results, $braceRegexp) {
            // if pattern doesn't have braces at all then it means that the expansion has finished,
            // there's no more brace patterns to be expanded, return the clear pattern
            if (!preg_match($braceRegexp, $pattern)) {
                return $pattern;
            }

            preg_replace_callback($braceRegexp, function($matches) use (&$results, $pattern, $self) {
                $options = explode(',', $matches[1]);
                foreach ($options as $option) {
                    // replace the braces group with one of the options
                    // and pass it recursively to the expander
                    // to make sure all other potential brace groups are expanded
                    $expandedPattern = str_replace($matches[0], $option, $pattern);
                    $newPattern = $self($expandedPattern, $self);

                    // the expander will only return a non-empty pattern when it has finished expanding all groups
                    // at which point we can add it to the list of results
                    if ($newPattern) {
                        $results[] = $newPattern;
                    }
                }
            }, $pattern);
            return null;
        };

        foreach ($patterns as $pattern) {
            $expander($pattern, $expander);
        }

        return $results;
    }

    /**
     * Helper method that assits `::glob()` in handling "glob star" patterns.
     *
     * @param  array|string $patterns An array of glob patterns or a single pattern.
     * @param  integer      $flags    Glob flags.
     * @return array
     */
    private static function globStarExpand($patterns, $flags = 0) {
        $patterns = is_array($patterns) ? $patterns : array($patterns);
        $results = array();

        $braceRegexp = '/\{(.+)?([\*]{2}[^,]?)(.?)\}/i';

        foreach ($patterns as $pattern) {
            // check if globstar is inside braces
            if (preg_match($braceRegexp, $pattern)) {
                // if GLOB_BRACE is not defined (e.g. Alpine Linux) then all patterns must have been expanded before
                // using ::globBraceExpand(), so we should ignore any patterns with braces in them and just use the
                // expanded ones, otherwise we face unforeseen consequences (e.g. fetching whole file tree from root)
                if (!defined('GLOB_BRACE') || !GLOB_BRACE) {
                    continue;
                }

                // otherwise check if GLOB_BRACE was indeed passed as a flag and we should handle it
                if ($flags & GLOB_BRACE) {
                    // extract the globstar from inside the braces and add a new pattern to patterns list
                    $results[] = preg_replace_callback('/(.+)?\{(.+)?([\*]{2}[^,]?)(.?)\}(.?)/i', function($matches) {
                        $brace = '{'. $matches[2] . $matches[4] .'}';
                        if ($brace === '{,}' || $brace === '{}') {
                            $brace = '';
                        }

                        $pattern = $matches[1] . $brace . $matches[5];
                        return str_replace('//', '/', $pattern);
                    }, $pattern);

                    // and now change the braces in the main pattern to globstar
                    $pattern = preg_replace_callback($braceRegexp, function($matches) {
                        return $matches[2];
                    }, $pattern);
                }
            }

            $pos = stripos($pattern, '**');

            // just need to make sure this is indeed globstar pattern
            // (as it might be one of results of running ::globBraceExpand())
            if ($pos === false) {
                continue;
            }

            $rootPattern = substr($pattern, 0, $pos) .'*';
            $restPattern = substr($pattern, $pos + 2);

            while($dirs = glob($rootPattern, GLOB_ONLYDIR)) {
                $rootPattern = $rootPattern .'/*';

                foreach($dirs as $dir) {
                    $results[] = $dir . $restPattern;
                }
            }
        }

        return $results;
    }
    
}

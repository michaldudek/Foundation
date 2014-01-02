<?php
/**
 * A set of array utility functions.
 *
 * @package Foundation
 * @subpackage Utils
 * @author Michał Dudek <michal@michaldudek.pl>
 *
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation\Utils;

use MD\Foundation\Exceptions\InvalidArgumentException;

/**
 * @static
 */
class ArrayUtils
{

    const JOIN_INNER = 'inner';
    const JOIN_OUTER = 'outer';

    /**
     * Check whether the given array is a collection of data, ie. multidimensional array with a list of data rows.
     *
     * @param array $array Array to check.
     * @return bool
     */
    public static function isCollection(array $array) {
        return (count($array) !== array_reduce(array_keys($array), function($a, $b) {
            return $a === $b ? $a + 1 : 0;
        }, 0)) ? false : true;
    }

    /**
     * Resets keys of an array to numerical values starting with 0.
     *
     * @param array $array Array to reset.
     * @return array
     */
    public static function resetKeys(array $array) {
        $return = array();

        foreach($array as &$row) {
            $return[] = $row;
        }

        return $return;
    }

    /**
     * Returns an array with a list of all values (not unique) assigned to a key in a collection.
     *
     * @param array $array Array to filter from.
     * @param string $key Key to filter by.
     * @param bool $preserveKey [optional] Should the level 1 key be preserved or not? Default: false.
     * @return array
     */
    public static function pluck(array $array, $key, $preserveKey = false) {
        $return = array();

        foreach($array as $k => &$row) {
            if (isset($row[$key])) {
                if ($preserveKey) {
                    $return[$k] = $row[$key];
                } else {
                    $return[] = $row[$key];
                }
            }
        }

        return $return;
    }

    /**
     * Alias for ::pluck().
     * 
     * @param array $array Array to filter from.
     * @param string $key Key to filter by.
     * @param bool $preserveKey [optional] Should the level 1 key be preserved or not? Default: false.
     * @return array
     *
     * @deprecated Please use ::pluck() instead.
     */
    public static function keyFilter(array $array, $key, $preserveKey = false) {
        return static::pluck($array, $key, $preserveKey);
    }

    /**
     * Filters out all values from a multidimensional array that match the given value of they given associative level 2 key.
     *
     * @param array $array Array to filter from.
     * @param string $key Key to filter by.
     * @param mixed $value Value to filter by.
     * @param bool $preserveKey [optional] Should the level 1 key be preserved or not? Default: false.
     * @return array
     */
    public static function filter(array $array, $key, $value, $preserveKey = false) {
        $return = array();

        foreach($array as $k => &$row) {
            if ((isset($row[$key])) AND ($row[$key] == $value)) {
                if ($preserveKey) {
                    $return[$k] = $row;
                } else {
                    $return[] = $row;
                }
            }
        }

        return $return;
    }

    /**
     * Alias for ::filter.
     * 
     * @param array $array Array to filter from.
     * @param string $key Key to filter by.
     * @param mixed $value Value to filter by.
     * @param bool $preserveKey [optional] Should the level 1 key be preserved or not? Default: false.
     * @return array
     *
     * @deprecated Please use ::filter() instead.
     */
    public static function filterByKeyValue(array $array, $key, $value, $preserveKey = false) {
        return static::filter($array, $key, $value, $preserveKey);
    }

    /**
     * Assigns a value under a key of a collection to its main (top level) key.
     *
     * @param array $array Array to be exploded.
     * @param string $key Key on which to explode.
     * @return array
     *
     * @throws \RuntimeException When at least one row of the $array doesn't have the $key.
     * @throws \RuntimeException When values of $key are not unique.
     */
    public static function indexBy(array $array, $key) {
        $return = array();

        foreach($array as $k => &$row) {
            if (isset($row[$key])) {
                if (!isset($return[$row[$key]])) {
                    $return[$row[$key]] = $row;
                } else {
                    throw new \RuntimeException('Value of a key "'. $key .'" in rows of array sent to '. get_called_class() .'::indexBy() are not unique and therefore some rows would be missing from the returned array.');
                }
            } else {
                throw new \RuntimeException('At least one row of an array sent to '. get_called_class() .'::indexBy() hasn\'t got a key "'. $key .'" and therefore would be missing from the return value.');
            }
        }

        return $return;
    }

    /**
     * Alias for ::indexBy.
     * 
     * @param  array  $array Array to be indexed.
     * @param  string $key Key to index by.
     * @return array
     *
     * @deprecated Please use ::indexBy() instead.
     */
    public static function keyExplode(array $array, $key) {
        return static::indexBy($array, $key);
    }

    /**
     * Group all items from the collection by the value of a specific key.
     *
     * @param array $array Array to parse.
     * @param string $key Key to group by.
     * @param bool $preserveKey [optional] Preserve keys? Default: false.
     * @return array
     */
    public static function groupBy(array $array, $key, $preserveKey = false) {
        $return = array();

        foreach($array as $k => &$row) {
            if (!isset($row[$key])) {
                continue;
            }

            if (!isset($return[$row[$key]])) {
                $return[$row[$key]] = array();
            }

            if ($preserveKey) {
                $return[$row[$key]][$k] = $row;
            } else {
                $return[$row[$key]][] = $row;
            }
        }

        return $return;
    }

    /**
     * Alias for ::groupBy().
     * 
     * @param array $array Array to parse.
     * @param string $key Key to group by.
     * @param bool $preserveKey [optional] Preserve keys? Default: false.
     * @return array
     *
     * @deprecated Please use ::groupBy() instead.
     */
    public static function categorizeByKey(array $array, $key, $preserveKey = false) {
        return static::groupBy($array, $key, $preserveKey);
    }

    /**
     * Implodes all fields with a specific key from a multidimensional array.
     *
     * @param array $array Array to implode from.
     * @param string $key Key to implode by.
     * @param string $separator [optional] Separator to implode by. Default: ",".
     * @return string
     */
    public static function implodeByKey(array $array, $key, $separator = ',') {
        return implode($separator, static::keyFilter($array, $key));
    }

    /**
     * Searches a multidimensional array (a collection, but not necessarily) for a specific value on a key and returns top key (first occurrence!).
     *
     * @param array $array Array to search through.
     * @param string $key Key of which value to look for.
     * @param string $value Value to look for.
     * @return string|int|bool Key name found (string/int) or bool false if not found.
     */
    public static function search(array $array, $key, $value) {
        foreach ($array as $k => &$row) {
            if ((isset($row[$key])) AND ($row[$key] === $value)) {
                return $k;
            }
        }
        return false;
    }

    /**
     * Get the numerical index for an associative key.
     *
     * @param array $array Array to search through.
     * @param string $key Key name.
     * @return int|bool Int for the position of the key if it was found, bool false if it wasn't found.
     */
    public static function keyPosition(array $array, $key) {
        $x = 0;

        foreach($array as $i => &$row) {
            if ($key == $i) {
                return $x;
            } else {
                $x++;
            }
        }

        return false;
    }

    /**
     * Removes a key from a collection.
     *
     * @param array $array Array to be parsed.
     * @param string $key Key to be removed.
     * @return array
     */
    public static function keyRemove(array $array, $key) {
        foreach($array as $k => &$row) {
            if (isset($row[$key])) {
                unset($row[$key]);
            }
        }
        return $array;
    }

    /**
     * Adds a key to a collection.
     *
     * @param array $array Array to be parsed.
     * @param key $key Key to be added.
     * @param mixed $value [optional] Optional value to be set under this key. Default null.
     * @return array
     */
    public static function keyAdd(array $array, $key, $value = null) {
        foreach($array as $k => &$row) {
            $row[$key] = $value;
        }

        return $array;
    }

    /**
     * Sorts a multidimensional array by key (2-dimensional).
     *
     * @param array $array Array to sort.
     * @param string $key Key to sort by.
     * @param bool $reverse [optional] True for descending order, false for ascending. Default: false.
     * @return array
     */
    public static function multiSort(array $array, $key, $reverse = false) {
        if (empty($array)) {
            return array();
        }

        $categorized = static::categorizeByKey($array, $key);
        if ($reverse) {
            krsort($categorized);
        } else {
            ksort($categorized);
        }

        $sorted = array();
        foreach($categorized as $cat => $items) {
            foreach($items as $item) {
                $sorted[] = $item;
            }
        }

        return $sorted;
    }

    /**
     * Sorts an array of paths in either "child first" or "root first" order.
     *
     * @param  array   $paths     Array of paths (strings).
     * @param  boolean $rootFirst [optional] Root first? Default: false (child first).
     * @param  boolean $separator [optional] Used path separator. Default: DIRECTORY_SEPARATOR.
     * @return array
     */
    public static function sortPaths(array $paths, $rootFirst = false, $separator = DS) {
        usort($paths, function($a, $b) use ($rootFirst, $separator) {
            $a = trim(trim($a, $separator));
            $b = trim(trim($b, $separator));

            if ($a === $b) {
                return 0;
            }

            $aPath = explode($separator, $a);
            $bPath = explode($separator, $b);

            // find first distinct path element
            $aNode = array_shift($aPath);
            $bNode = array_shift($bPath);

            while($aNode === $bNode) {
                $aNode = array_shift($aPath);
                $bNode = array_shift($bPath);
            }

            // if one of the paths has finished then it means they're in root
            if (empty($aPath) && !empty($bPath)) {
                return $rootFirst ? -1 : 1;
            } else if (empty($bPath) && !empty($aPath)) {
                return $rootFirst ? 1 : -1;
            }

            // normal sort comparison based on first distinct element
            $order = array($aNode, $bNode);
            sort($order);
            return $order[0] === $aNode ? -1 : 1;
        });
        return $paths;
    }

    /**
     * Push a value after another one in an array
     *
     * @param array $array Array to push into.
     * @param mixed $input Value to push.
     * @param int/string $position Position or key after which to push the value.
     * @param string $key [optional] Specific key assigned to the value (if wanted). Default: null.
     * @return array
     */
    public static function pushAfter(array $array, $input, $position, $key = null) {
        $input = ($key) ? array($key => $input) : array($input);

        // if position is integer then its a simple matter of slicing in "half" and adding the input inside
        if (is_int($position)) {
            return array_merge(array_slice($array, 0, $position + 1), $input, array_slice($array, $position + 1));
        }

        // let's look for the associative key to insert after
        $foundPosition = false;
        $return = array();
        foreach($array as $k => &$row) {
            $return[$k] = $row;
            // if found then insert after
            if ($k == $position) {
                $return = array_merge($return, $input);
                $foundPosition = true;
            }
        }

        // if position (key) found then return the new array and if not then add the input to the end
        return ($foundPosition) ? $return : array_merge($return, $input);
    }

    /**
     * Filters the array so that it contains only the keys from the $allowedKeys list.
     *
     * @param array $array Array to be filtered.
     * @param array $allowedKeys List of keys that are allowed in the $array
     * @return array
     */
    public static function filterKeys(array $array, array $allowedKeys) {
        foreach($array as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Flattens an array, ie. makes it 1-dimensional.
     *
     * @param array $array Array to be flattened.
     * @return array
     */
    public static function flatten(array $array) {
        $flat = array();

        $flatter = function(array $arr, $self) use (&$flat) {
            foreach($arr as $item) {
                if (is_array($item)) {
                    $self($item, $self);
                } else {
                    $flat[] = $item;
                }
            }
        };

        $flatter($array, $flatter);

        return $flat;
    }

    /**
     * Performs a merge between n arrays, where the last array has the highest priority.
     *
     * @param array $array1
     * @param array $array2
     * @param array $array3
     * ...
     * @return array
     */
    public static function merge() {
        $target = array();
        $arrays = func_get_args();

        foreach($arrays as $i => $array) {
            if (!is_array($array)) {
                throw new InvalidArgumentException('array', $array, $i);
            }

            // if $array is a collection array then use the standard PHP array_merge (to add it at the end)
            if (static::isCollection($array)) {
                $target = array_merge($target, $array);
            } else {
                $target = static::mergeDeep($target, $array);
            }
        }

        return $target;
    }

    /**
     * Performs a deep merge between two arrays.
     *
     * @param array $into Array to merge into.
     * @param array $from Array to merge to.
     * @return array
     */
    public static function mergeDeep(array $into, array $from) {
        foreach($from as $key => $value) {
            if (is_array($value)) {
                $into[$key] = (isset($into[$key]) && is_array($into[$key])) ? static::mergeDeep($into[$key], $value) : $value;
            } else {
                $into[$key] = $value;
            }
        }

        return $into;
    }

    /**
     * Joins the second collection into the first based on the given key. Default is outer join meaning
     * that if matching row wasn't found in the second collection the row in the first collection will still
     * be displayed (this can be altered by setting the 6th argument to 'inner').
     *
     * @param array $into Collection of data in which to put values of $from.
     * @param array $from Collection of data from which get values to put into $into.
     * @param string $onKey What key from $into to compare on?
     * @param string $intoKey Into what key to put values from $from.
     * @param string $fromKey What key from $from to compare on?
     * @param string $type [optional] Type of join. Default is ArrayUtils::JOIN_OUTER. Can also be ArrayUtils::JOIN_INNER which will remove all items
     *                  from $into that haven't got values in $from. Default: ArrayUtils::JOIN_OUTER.
     * @return array
     */
    public static function join(array $into, array $from, $onKey, $intoKey, $fromKey, $type = self::JOIN_OUTER) {
        if (empty($into)) {
            return array();
        }

        $from = static::keyExplode($from, $fromKey);

        foreach($into as $k => &$row) {
            if (isset($from[$row[$onKey]])) {
                $row[$intoKey] = $from[$row[$onKey]];
            } elseif ($type === self::JOIN_INNER) {
                unset($into[$k]);
            }
        }

        return $into;
    }

    /**
     * Check whether the specified keys are set inside the given array and are not empty (strings are trimmed before check).
     *
     * Returns boolean true if all is correct, false otherwise.
     *
     * @param array $array Array to check.
     * @param array $keys Array of keys to check.
     * @return bool
     */
    public static function checkValues(array $array, array $keys) {
        foreach($keys as $key) {
            if (
                (!isset($array[$key]))
                || ($array[$key] !== false && empty($array[$key]))
                || (is_string($array[$key]) && (trim($array[$key]) === ''))
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Removes empty keys from an array.
     *
     * @param array $array Array to clean.
     * @return array
     */
    public static function cleanEmpty(array $array) {
        foreach($array as $key => $value) {
            if (is_string($value)) {
                $value = trim($value);
            }

            if (empty($value) && $value !== 0 && $value !== false) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * Parses the given array into a query string.
     *
     * @param array $array Array to be converted to query string.
     * @return string
     */
    public static function toQueryString(array $array) {
        return http_build_query($array);
    }

    /**
     * Converts the given array to an object. The conversion is "deep", ie. all dimensions will be converted.
     *
     * @param array $array Array to convert to object.
     * @param object $object [optional] Object to which assign the properties, usually for internal use. Default: null.
     * @return stdClass
     */
    public static function toObject(array $array, $object = null) {
        if ($object !== null && !is_object($object)) {
            throw new InvalidArgumentException('object', $object, 2);
        }

        $parent = ($object !== null) ? $object : new \stdClass();

        foreach($array as $key => $value) {
            // make sure the property exists if we're gonna go recursively through it
            if (!isset($parent->$key)) {
                $parent->$key = null;
            }

            if (is_array($value)) {
                $parent->$key = static::toObject($value, $parent->$key);
            } else {
                $parent->$key = $value;
            }
        }

        return $parent;
    }

    /**
     * Creates an array from a given object. The conversion is "deep", ie. all dimensions will be converted.
     *
     * @param object|array $object Object to be converted to an array or an array of objects.
     * @param array $parent [optional] Usually for internal use of the function. A reference to parent array. Default: array().
     * @param array $keys [optional] If you don't want the whole object converted to array, specify the names of keys
     *                    that you are interested in. Default: array().
     * @return array
     */
    public static function fromObject($object, array $parent = array(), array $keys = array()) {
        // maybe an array of objects has been passed?
        if (is_array($object)) {
            foreach($object as $key => $item) {
                $parent[$key] = (is_object($item) || is_array($item)) ? static::fromObject($item, array(), $keys) : $item;
            }
            return $parent;
        }

        // if not an array or object then throw exception
        if (!is_object($object)) {
            throw new InvalidArgumentException('object or array', $object);
        }

        // can object be converted to array?
        if (method_exists($object, 'toArray')) {
            $toArray = $object->toArray();
            if (is_array($toArray)) {
                // make the original object an array so we can iterate through all keys and transform their values as well
                $object = $toArray;
            }
        }

        // and finally typical handling of items
        foreach($object as $key => $value) {
            // check if included in the keys array (if any specified)
            if (!empty($keys) && !in_array($key, $keys)) {
                continue;
            }

            $parent[$key] = (is_object($value) || is_array($value)) ? static::fromObject($value) : $value;
        }

        return $parent;
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array   $array
     * @param  string  $prepend
     * @return array
     */
    public static function dot($array, $prepend = '')
    {
        $results = array();

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $results = array_merge(
                    $results,
                    self::dot($value, $prepend.$key.'.')
                );
            }
            else
            {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Returns value from the key at given path (using a dot notation).
     *
     * @param  array  $array Array to get the value from.
     * @param  string $key Key or path to key.
     * @param  mixed  $default Default value if key not found.
     * @return mixed
     */
    public static function get(array $array, $key, $default = null) {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Set an array item to a given value using a dot notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * Returns the array.
     *
     * @param  array  $array Array to which the key should be added. Passed by reference.
     * @param  string $key Key or path to key.
     * @param  mixed  $value Value to be set at the given path.
     * @return array
     */
    public static function set(array &$array, $key, $value) {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

}

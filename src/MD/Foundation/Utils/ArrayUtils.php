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

/**
 * @static
 */
class ArrayUtils
{
    
    /**
     * Check whether the given array is a collection of data, ie. multidimensional array with a list of data rows.
     * 
     * @param array $array Array to check.
     * @return bool True if it is a collection of data, false otherwise.
     */
    public static function isCollection($array) {
        return (!is_array($array) || count($array) !== array_reduce(array_keys($array), function($a, $b) {
            return $a === $b ? $a + 1 : 0;
        }, 0)) ? false : true;
    }
    
    /**
     * Resets keys of an array to numerical values starting with 0.
     * 
     * @param array $array Array to reset.
     * @return array
     */
    public static function resetKeys($array) {
        if (!is_array($array)) {
            return array();
        }
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
     * @param bool $preserveKey [optional] Should the level 1 key be preserved or not? Default false.
     * @return array Array with the list.
     */
    public static function keyFilter($array, $key, $preserveKey = false) {
        if (!is_array($array)) {
            return array();
        }

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
     * Filters out all values from a multidimensional array that match the given value of they given associative level 2 key.
     * 
     * @param array $array Array to filter from.
     * @param string $key Key to filter by.
     * @param mixed $value Value to filter by.
     * @param bool $preserveKey [optional] Should the level 1 key be preserved or not? Default false.
     * @return array Filtered array.
     */
    public static function filterByKeyValue(&$array, $key, $value, $preserveKey = false) {
        if (!is_array($array)) return array();
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
     * Assigns a value under a key of a collection to its main (top level) key.
     * 
     * @param array $array Array to be exploded.
     * @param string $key Key on which to explode.
     * @return array Exploded array.
     */
    public static function keyExplode(&$array, $key) {
        if (!is_array($array)) return array();
        $return = array();
        
        foreach($array as $k => &$row) {
            if (isset($row[$key])) {
                $return[$row[$key]] = $row;
            }
        }
        
        return $return;
    }
    
    /**
     * Categorize all items from the collection by the value of a specific key.
     * 
     * @param array $array Array to parse.
     * @param string $key Key to categorize by.
     * @param bool $preserveKey [optional] Preserve keys? Default false.
     * @return array Categorized array.
     */
    public static function categorizeByKey($array, $key, $preserveKey = false) {
        if (!is_array($array)) {
            return array();
        }

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
     * Implodes all fields with a specific key from a multidimensional array.
     * 
     * @param array $array Array to implode from.
     * @param string $key Key to implode by.
     * @param string $separator [optional] Separator to implode by. Default ",".
     * @return string Imploded string.
     */
    public static function implodeByKey(&$array, $key, $separator = ',') {
        if (!is_array($array)) return '';
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
    public static function search(&$array, $key, $value) {
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
    public static function keyPosition(&$array, $key) {
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
     * @return array The given collection array without the keys.
     */
    public static function keyRemove(&$array, $key) {
        if (!is_array($array)) return array();
        
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
     * @return array The given collection with added key.
     */
    public static function keyAdd(&$array, $key, $value = null) {
        if (!is_array($array)) return array();
        
        foreach($array as $k => &$row) {
            $row[$key] = $value;
        }
        
        return $array;
    }
    
    /**
     * Switches values and keys of an array. Has to be one dimensional array!
     * 
     * @param array $array 1-dimensional array!
     * @return array New array.
     */
    public static function switchKeyValue(&$array) {
        if (!is_array($array)) return array();
        
        $return = array();
        foreach($array as $k => &$row) {
            $return[$row] = $k;
        }
        
        return $return;
    }
    
    /**
     * Sorts a multidimensional array by key (2-dimensional).
     * 
     * @param array $array Array to sort.
     * @param string $key Key to sort by.
     * @param bool $reverse [optional] True for descending order, false (default) for ascending.
     * @return array Sorted array.
     */
    public static function multiSort(&$array, $key, $reverse = false) {
        if ((!is_array($array)) OR (empty($array))) {
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
     * Push a value after another one in an array
     * 
     * @param array $array Array to push into.
     * @param mixed $input Value to push.
     * @param int/string $position Position or key after which to push the value/
     * @param string $key [optional] Specific key assigned to the value (if wanted).
     * @return array Array with additional row.
     */
    public static function pushAfter(&$array, $input, $position, $key = false) {
        if (!is_array($array)) return array();
        
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
     * Helper property for the SplotArray::flatten method.
     */
    private static $_flatArray = array();
    
    /**
     * Flattens an array, ie. makes it 1-dimensional.
     * 
     * @param array $array Array to be flattened.
     * @return array
     */
    public static function flatten($array) {
        if (!is_array($array)) return $array;
        
        // make sure the helper property is empty
        static::$_flatArray = array();
        static::_flattenHelper($array);
        $flatArray = static::$_flatArray;
        static::$_flatArray = array();
        return $flatArray;
    }
    
    /**
     * Helper method for static::flatten()
     * 
     * @param array $array
     */
    private static function _flattenHelper($array) {
        if (!is_array($array)) return $array;
        
        foreach($array as $row) {
            if (is_array($row)) {
                static::_flattenHelper($row);
            } else {
                static::$_flatArray[] = $row;
            }
        }
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
                throw new \InvalidArgumentException('Argument #'. $i .' supplied to '. get_called_class() .'::merge() must be array, '. get_type($array) .' given.');
            }

            $target = static::mergeDeep($target, $array);
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
                $into[$key] = (isset($into[$key])) ? static::mergeDeep($into[$key], $value) : $value;
            } else {
                $into[$key] = $value;
            }
        }

        return $into;
    }
    
    /**
     * Joins the second collection into the first based a given key. Default is outer join meaning
     * that if matching row wasn't found in the second collection the row in the first collection will still
     * be displayed (this can be altered by setting the 6th argument to 'inner').
     * 
     * @param array $array1 Collection of data in which to put values of $array2.
     * @param array $array2 Collection of data from which get values to put into $array1.
     * @param string $onKey Key on which to join (ie. merge occurs when the value of this key in $array1 is equal to 
     * @param string $underKey [optional] Under what key to put values from $array2. By default it's null which means
     *                  that the values from both collections will be merged and values from $array2 will overwrite 
     *                  values from $array1.
     * @param string $array2Key [optional] If key on which to join is different for $array2 specify it here.
     * @param string $type [optional] Type of join. Default is 'outer'. Can also be 'inner' which will remove all items
     *                  from $array1 that haven't got values in $array2.
     * @return array
     */
    public static function join($array1, $array2, $onKey, $underKey = null, $array2Key = null, $type = 'outer') {
        if ((!is_array($array1)) OR (empty($array1))) return array();
        
        $array2Key = ($array2Key) ? $array2Key : $onKey;
        $array2 = static::keyExplode($array2, $array2Key);
        
        foreach($array1 as $k => &$row) {
            if (isset($array2[$row[$onKey]])) {
                if ($underKey) {
                    $row[$underKey] = $array2[$row[$onKey]];
                } else {
                    $row = array_merge($row, $array2[$row[$onKey]]);
                }
            } elseif ($type == 'inner') {
                unset($array1[$k]);
            }
        }
        
        return $array1;
    }
    
    /**
     * Check whether the specified keys are set inside the given array.
     * 
     * @param array $array Array to check.
     * @param array $keys Array of keys to check.
     * @return bool True if all keys are set, false if at least one is missing.
     */
    public static function checkValues(&$array, $keys) {
        if (!is_array($array)) return false;
        
        foreach($keys as $key) {
            if (
                (!isset($array[$key]))
                OR (empty($array[$key]))
                OR (is_string($array[$key]) AND (trim($array[$key]) == ''))
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Removes empty rows from an array.
     * 
     * @param array $array Array to clean.
     * @return array
     */
    public static function cleanEmpty(&$array) {
        foreach($array as $key => &$value) {
            $value = trim($value);
            if (empty($value)) unset($array[$key]);
        }
        return $array;
    }

    /**
     * Parses the given array into a query string.
     * 
     * @param array $array Array to be converted to query string.
     * @return string
     */
    public static function toQueryString(&$array) {
        return http_build_query($array);
    }
    
    /**
     * Converts the given array to an object. The conversion is "deep", ie. all dimensions will be converted.
     * 
     * @param array $array Array to convert to object.
     * @param object $object [optional] Object to which assign the properties.
     * @return object New object from the given array.
     */
    public static function toObject(&$array, $object = false) {
        $parent = ($object) ? $object : new stdClass();
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
     * @param mixed $object Object to be converted to an array or an array of objects.
     * @param array $array [optional] Usually for internal use of the function. A reference to parent array.
     * @param array $keys [optional] If you don't want the whole object converted to array, specify the names of keys that you are interested in (only works for 1st level keys).
     * @return array
     * 
     * @todo Remove old MDModel case.
     */
    public static function fromObject($object, $array = false, $keys = false) {
        // maybe an array of objects has been passed?
        if (is_array($object)) {
            foreach($object as &$item) {
                $item = static::fromObject($item, null, $keys);
            }
            return $object;
        }
        
        // if not an array or object then just return itself
        if (!is_object($object)) return $object;
        
        // proper conversion
        $parent = ($array) ? $array : array();

        // can object be converted to array?
        if (method_exists($object, 'toArray')) {
            return $object->toArray();
        }
        
        // and finally typical handling of items
        foreach($object as $varName => $value) {
            // check if included in the keys array (if any specified)
            if ($keys AND is_array($keys) AND !in_array($varName, $keys)) continue;
            
            $parent[$varName] = null;
            $parent[$varName] = (is_object($value)) ? static::fromObject($value, $parent[$varName]) : ((is_array($value)) ? static::fromObject($value) : $value);
        }
        
        return $parent;
    }
    
}
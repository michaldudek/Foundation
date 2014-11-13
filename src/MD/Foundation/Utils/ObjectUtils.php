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

use MD\Foundation\Exceptions\InvalidArgumentException;
use MD\Foundation\Utils\ArrayUtils;
use MD\Foundation\Utils\StringUtils;

/**
 * A set of object and array of objects utility functions.
 *
 * All methods in this class when attempting to access object properties
 * first try to use getters (their names are built with [`::getter`](#getter))
 * method) and then try to access them as public properties.
 */
class ObjectUtils
{
    
    /**
     * Returns an array with a list of all values (not unique) assigned to a key in a collection of objects.
     * 
     * @param array|object $objects Collection of objects - either an array or a collection object that implements `toArray` method.
     * @param string $key Get values of this property.
     * @return array
     * 
     * @throws InvalidArgumentException When the `$objects` argument is not an array or cannot be converted to an array.
     */
    public static function pluck($objects, $key) {
        // convert to array, for example for Doctrine collections
        if (is_object($objects) && method_exists($objects, 'toArray')) {
            $objects = $objects->toArray();
        }

        if (!is_array($objects)) {
            throw new InvalidArgumentException('array or object convertible to array', $objects);
        }

        // build a getter name to always try with getter
        $getter = static::getter($key);
        $return = array();
        
        foreach($objects as $object) {
            if (is_array($object)) {
                if (isset($object[$key])) {
                    $val = $object[$key];
                } else {
                    continue; // can't get any value, move on
                }
            } elseif (method_exists($object, $getter)) {
                $val = $object->$getter();
            } elseif (isset($object->$key)) {
                $val = $object->$key;
            } else {
                continue; // can't get any value, move on
            }

            $return[] = $val;
        }
        
        return $return;     
    }

    /**
     * Alias for [`::pluck()`](#pluck).
     * 
     * @param array|object $objects Collection of objects - either an array or a collection object that implements `toArray` method.
     * @param string $key Get values of this property.
     * @return array
     *
     * @deprecated Please use [`::pluck()`](#pluck) instead.
     */
    public static function keyFilter($objects, $key) {
        return static::pluck($objects, $key);        
    }
    
    /**
     * Assigns a value under a key of a collection of objects to its main (top level) key.
     * 
     * @param array|object $objects Collection of objects - either an array or a collection object that implements `toArray` method.
     * @param string $key Key on which to index by.
     * @return array
     * 
     * @throws InvalidArgumentException When the `$objects` argument is not an array or cannot be converted to an array.
     */
    public static function indexBy($objects, $key) {
        // convert to array, for example for Doctrine collections
        if (is_object($objects) && method_exists($objects, 'toArray')) {
            $objects = $objects->toArray();
        }

        if (!is_array($objects)) {
            throw new InvalidArgumentException('array or object convertible to array', $objects);
        }

        // build a getter name to always try with getter
        $getter = static::getter($key);
        $return = array();
        
        foreach($objects as $k => $object) {
            if (is_array($object)) {
                if (isset($object[$key])) {
                    $val = $object[$key];
                } else {
                    continue; // can't get any value, move on
                }
            } elseif (method_exists($object, $getter)) {
                $val = $object->$getter();
            } elseif (isset($object->$key)) {
                $val = $object->$key;
            } else {
                continue; // can't get any value, move on
            }

            $return[$val] = $object;
        }
        
        return $return;
    }

    /**
     * Alias for [`::indexBy()`](#indexBy).
     * 
     * @param array|object $objects Collection of objects - either an array or a collection object that implements `toArray` method.
     * @param string $key Key on which to index by.
     * @return array
     *
     * @deprecated Please use [`::indexBy()`](#indexBy) instead.
     */
    public static function keyExplode($objects, $key) {
        return static::indexBy($objects, $key);
    }
    
    /**
     * Filters out all values from a multidimensional array that contains objects that match the given value of the given associative level 2 key.
     * 
     * @param array|object $objects Collection of objects - either an array or a collection object that implements `toArray` method.
     * @param string $key Key to filter by.
     * @param mixed $value Value to filter by.
     * @param bool $preserveKey [optional] Should the level 1 key be preserved or not? Default: `false`.
     * @return array
     * 
     * @throws InvalidArgumentException When the `$objects` argument is not an array or cannot be converted to an array.
     */
    public static function filter($objects, $key, $value, $preserveKey = false) {
        // convert to array, for example for Doctrine collections
        if (is_object($objects) && method_exists($objects, 'toArray')) {
            $objects = $objects->toArray();
        }

        if (!is_array($objects)) {
            throw new InvalidArgumentException('array or object convertible to array', $objects);
        }

        // build a getter name to always try with getter
        $getter = static::getter($key);
        $return = array();
        
        foreach($objects as $k => $object) {
            if (is_array($object)) {
                if (isset($object[$key])) {
                    $val = $object[$key];
                } else {
                    continue; // can't get any value, move on
                }
            } elseif (method_exists($object, $getter)) {
                $val = $object->$getter();
            } elseif (isset($object->$key)) {
                $val = $object->$key;
            } else {
                continue; // can't get any value, move on
            }

            if ($val == $value) {
                if ($preserveKey) {
                    $return[$k] = $object;
                } else {
                    $return[] = $object;
                }
            }
        }
        
        return $return;
    }

    /**
     * Alias for [`::filter()`](#filter).
     * 
     * @param array|object $objects Collection of objects - either an array or a collection object that implements `toArray` method.
     * @param string $key Key to filter by.
     * @param mixed $value Value to filter by.
     * @param bool $preserveKey [optional] Should the level 1 key be preserved or not? Default: `false`.
     * @return array
     *
     * @deprecated Please use [`::filter()`](#filter) instead.
     */
    public static function filterByKeyValue($objects, $key, $value, $preserveKey = false) {
        return static::filter($objects, $key, $value, $preserveKey);
    }
    
    /**
     * Categorize all items from the collection of objects by the value of a specific key.
     * 
     * @param array|object $objects Collection of objects - either an array or a collection object that implements `toArray` method.
     * @param string $key Key to categorize by.
     * @param bool $preserveKey [optional] Preserve keys? Default: `false`.
     * @return array
     * 
     * @throws InvalidArgumentException When the `$objects` argument is not an array or cannot be converted to an array.
     */
    public static function groupBy($objects, $key, $preserveKey = false) {
        // convert to array, for example for Doctrine collections
        if (is_object($objects) && method_exists($objects, 'toArray')) {
            $objects = $objects->toArray();
        }

        if (!is_array($objects)) {
            throw new InvalidArgumentException('array or object convertible to array', $objects);
        }

        // build a getter name to always try with getter
        $getter = static::getter($key);
        $return = array();
        
        foreach($objects as $k => $object) {
            if (is_array($object)) {
                if (isset($object[$key])) {
                    $val = $object[$key];
                } else {
                    continue; // can't get any value, move on
                }
            } elseif (method_exists($object, $getter)) {
                $val = $object->$getter();
            } elseif (isset($object->$key)) {
                $val = $object->$key;
            } else {
                continue; // can't get any value, move on
            }

            // if first in category then prepare it in the results list
            if (!isset($return[$val])) {
                $return[$val] = array();
            }

            if ($preserveKey) {
                $return[$val][$k] = $object;
            } else{
                $return[$val][] = $object;
            }
        }
        
        return $return;
    }

    /**
     * Alias for [`::groupBy()`](#groupBy).
     * 
     * @param array|object $objects Collection of objects - either an array or a collection object that implements `toArray` method.
     * @param string $key Key to categorize by.
     * @param bool $preserveKey [optional] Preserve keys? Default: `false`.
     * @return array
     *
     * @deprecated Please use [`::groupBy()`](#groupBy) instead.
     */
    public static function categorizeByKey($objects, $key, $preserveKey = false) {
        return static::groupBy($objects, $key, $preserveKey);
    }
    
    /**
     * Sorts a collection of object based on the specified key.
     * 
     * @param array|object $objects Collection of objects - either an array or a collection object that implements `toArray` method.
     * @param string $key Key to sort by.
     * @param bool $reverse [optional] `true` for descending order, `false` for ascending. Default: `false`.
     * @return array
     * 
     * @throws InvalidArgumentException When the `$objects` argument is not an array or cannot be converted to an array.
     */
    public static function multiSort($objects, $key, $reverse = false) {
        // convert to array, for example for Doctrine collections
        if (is_object($objects) && method_exists($objects, 'toArray')) {
            $objects = $objects->toArray();
        }

        if (!is_array($objects)) {
            throw new InvalidArgumentException('array or object convertible to array', $objects);
        }

        // don't worry if objects are empty
        if (empty($objects)) {
            return $objects;
        }

        $categorized = static::categorizeByKey($objects, $key);
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
     * Resets keys of an array containing a collection of objects to numerical values starting with `0`.
     * 
     * @param array|object $objects Collection of objects - either an array or a collection object that implements `toArray` method.
     * @return array Resetted array.
     * 
     * @throws InvalidArgumentException When the `$objects` argument is not an array or cannot be converted to an array.
     */
    public static function resetKeys($objects) {
        // convert to array, for example for Doctrine collections
        if (is_object($objects) && method_exists($objects, 'toArray')) {
            $objects = $objects->toArray();
        }

        if (!is_array($objects)) {
            throw new InvalidArgumentException('array or object convertible to array', $objects);
        }

        return ArrayUtils::resetKeys($objects);
    }

    /**
     * Creates a getter name for the given object property name.
     *
     * Converts `snake_notation` to `camelCase`.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ObjectUtils::getter('property_name');
     *      // -> 'getPropertyName'
     * 
     * @param string $property Property name.
     * @return string
     */
    public static function getter($property) {
        return 'get'. ucfirst(StringUtils::toCamelCase($property, '_'));
    }

    /**
     * Creates a setter name for the given object property name.
     *
     * Converts `snake_notation` to `camelCase`.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ObjectUtils::setter('property_name');
     *      // -> `setPropertyName`
     * 
     * @param string $property Property name.
     * @return string
     */
    public static function setter($property) {
        return 'set'. ucfirst(StringUtils::toCamelCase($property, '_'));
    }
    
}

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

use StdClass;

use MD\Foundation\Exceptions\InvalidArgumentException;

/**
 * A set of array utility functions.
 */
class ArrayUtils
{

    const JOIN_INNER = 'inner';
    const JOIN_OUTER = 'outer';

    /**
     * Check whether the given array is a collection of data, ie. multidimensional array with a list of data rows.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::isCollection(array(
     *          array('id' => 1),
     *          array('id' => 2),
     *          array('id' => 3)
     *      ));
     *      // -> true
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::isCollection(array(
     *          'id' => 1,
     *          'title' => 'Lorem ipsum'
     *      ));
     *      // -> false
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
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::resetKeys(array(
     *          'a' => 0,
     *          'b' => 3,
     *          'c' => '8'
     *      ));
     *      // -> array(0, 3, '8')
     *
     * @param array $array Array to reset.
     * @return array
     */
    public static function resetKeys(array $array) {
        return array_values($array);
    }

    /**
     * Returns an array with a list of all (non-unique) values assigned to a key in a collection.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::pluck(array(
     *          array('id' => 4, 'title' => 'Lipsum', 'author' => 'John'),
     *          array('id' => 3, 'title' => 'Lorem ipsum', 'author' => 'Jane'),
     *          array('id' => 2, 'title' => 'Dolor sit amet', 'author' => 'Doe')
     *      ), 'title');
     *      // -> array('Lipsum', 'Lorem ipsum', 'Dolor sit amet')
     *
     * @param array $array Array collection to get the values from.
     * @param string $key Get values of this key.
     * @param bool $preserveIndex [optional] Should the collection index be preserved or not? Default: `false`.
     * @return array
     */
    public static function pluck(array $array, $key, $preserveIndex = false) {
        $return = array();

        foreach($array as $k => &$row) {
            if (isset($row[$key])) {
                if ($preserveIndex) {
                    $return[$k] = $row[$key];
                } else {
                    $return[] = $row[$key];
                }
            }
        }

        return $return;
    }

    /**
     * Alias for [`::pluck()`](#pluck).
     * 
     * @param array $array Array collection to get the values from.
     * @param string $key Get values of this key.
     * @param bool $preserveIndex [optional] Should the collection index be preserved or not? Default: `false`.
     * @return array
     *
     * @deprecated Please use [`::pluck()`](#pluck) instead.
     */
    public static function keyFilter(array $array, $key, $preserveIndex = false) {
        return static::pluck($array, $key, $preserveIndex);
    }

    /**
     * Returns all values from an array collection that match the given value of the given associative level 2 key.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::filter(array(
     *          array('id' => 3, 'title' => 'Lorem ipsum', 'featured' => true),
     *          array('id' => 2, 'title' => 'Lipsum', 'featured' => true),
     *          array('id' => 4, 'title' => 'Dolor sit amet', 'featured' => false),
     *          array('id' => 6, 'title' => 'Adipiscit elit', 'featured' => true)
     *      ), 'featured', true);
     *      // -> array(
     *      //      array('id' => 3, 'title' => 'Lorem ipsum', 'featured' => true),
     *      //      array('id' => 2, 'title' => 'Lipsum', 'featured' => true),
     *      //      array('id' => 6, 'title' => 'Adipiscit elit', 'featured' => true)
     *      // )
     *
     * @param array $array Array to filter from.
     * @param string $key Key to filter by.
     * @param mixed $value Value to filter by.
     * @param bool $preserveIndex [optional] Should the index of the array collection be preserved or not? Default: `false`.
     * @return array
     */
    public static function filter(array $array, $key, $value, $preserveIndex = false) {
        $return = array();

        foreach($array as $k => &$row) {
            if ((isset($row[$key])) && ($row[$key] == $value)) {
                if ($preserveIndex) {
                    $return[$k] = $row;
                } else {
                    $return[] = $row;
                }
            }
        }

        return $return;
    }

    /**
     * Alias for [`::filter()`](#filter).
     * 
     * @param array $array Array to filter from.
     * @param string $key Key to filter by.
     * @param mixed $value Value to filter by.
     * @param bool $preserveIndex [optional] Should the index of the array collection be preserved or not? Default: `false`.
     * @return array
     *
     * @deprecated Please use [`::filter()`](#filter) instead.
     */
    public static function filterByKeyValue(array $array, $key, $value, $preserveIndex = false) {
        return static::filter($array, $key, $value, $preserveIndex);
    }

    /**
     * Assigns a value under a key of an array collection to its main (top level) key.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::indexBy(array(
     *          array('id' => 3, 'title' => 'Lorem ipsum', 'featured' => true),
     *          array('id' => 2, 'title' => 'Lipsum', 'featured' => true),
     *          array('id' => 4, 'title' => 'Dolor sit amet', 'featured' => false),
     *          array('id' => 6, 'title' => 'Adipiscit elit', 'featured' => true)
     *      ), 'id');
     *      // -> array(
     *      //      3 => array('id' => 3, 'title' => 'Lorem ipsum', 'featured' => true),
     *      //      2 => array('id' => 2, 'title' => 'Lipsum', 'featured' => true),
     *      //      4 => array('id' => 4, 'title' => 'Dolor sit amet', 'featured' => false),
     *      //      6 => array('id' => 6, 'title' => 'Adipiscit elit', 'featured' => true)
     *      // )
     *
     * @param array $array Array to be re-indexed.
     * @param string $key Key on which to index by.
     * @return array
     *
     * @throws \RuntimeException When at least one row of the `$array` doesn't have the `$key`.
     * @throws \RuntimeException When values of `$key` are not unique.
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
     * Alias for [`::indexBy()`](#indexBy).
     * 
     * @param array $array Array to be re-indexed.
     * @param string $key Key on which to index by.
     * @return array
     *
     * @deprecated Please use [`::indexBy()`](#indexBy) instead.
     */
    public static function keyExplode(array $array, $key) {
        return static::indexBy($array, $key);
    }

    /**
     * Group all items from the array collection by the value of a key.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::groupBy(array(
     *          array('id' => 3, 'title' => 'Lorem ipsum', 'categoryId' => 4),
     *          array('id' => 2, 'title' => 'Lipsum', 'categoryId' => 2),
     *          array('id' => 4, 'title' => 'Dolor sit amet', 'categoryId' => 3),
     *          array('id' => 6, 'title' => 'Adipiscit elit', 'categoryId' => 4),
     *          array('id' => 1, 'title' => 'Aequetam alitat', 'categoryId' => 3)
     *      ), 'categoryId');
     *      // -> array(
     *      //      4 => array(
     *      //          array('id' => 3, 'title' => 'Lorem ipsum', 'categoryId' => 4),
     *      //          array('id' => 6, 'title' => 'Adipiscit elit', 'categoryId' => 4)
     *      //      ),
     *      //      2 => array(
     *      //          array('id' => 2, 'title' => 'Lipsum', 'categoryId' => 2)
     *      //      ),
     *      //      3 => array(
     *      //          array('id' => 4, 'title' => 'Dolor sit amet', 'categoryId' => 3),
     *      //          array('id' => 1, 'title' => 'Aequetam alitat', 'categoryId' => 3)
     *      //      )
     *      // )
     *
     * @param array $array Array collection.
     * @param string $key Key to group by.
     * @param bool $preserveIndex [optional] Should the index of the array collection be preserved or not? Default: `false`.
     * @return array
     */
    public static function groupBy(array $array, $key, $preserveIndex = false) {
        $return = array();

        foreach($array as $k => &$row) {
            if (!isset($row[$key])) {
                continue;
            }

            if (!isset($return[$row[$key]])) {
                $return[$row[$key]] = array();
            }

            if ($preserveIndex) {
                $return[$row[$key]][$k] = $row;
            } else {
                $return[$row[$key]][] = $row;
            }
        }

        return $return;
    }

    /**
     * Alias for [`::groupBy()`](#groupBy).
     * 
     * @param array $array Array collection.
     * @param string $key Key to group by.
     * @param bool $preserveIndex [optional] Should the index of the array collection be preserved or not? Default: `false`.
     * @return array
     *
     * @deprecated Please use [`::groupBy()`](#groupBy) instead.
     */
    public static function categorizeByKey(array $array, $key, $preserveIndex = false) {
        return static::groupBy($array, $key, $preserveIndex);
    }

    /**
     * Implodes values of an array, but allows alternative separator between last two elements.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::implode(array('a', 'b', 'c', 'd'), ', ', ' and ');
     *      // -> 'a, b, c and d'
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::implode(array('a', 'b'), ', ', ' and ');
     *      // -> 'a and b'
     * 
     * @param  array  $array         Array to be imploded.
     * @param  string $separator     [optional] Separator to implode with. Default: `,`.
     * @param  string $lastSeparator [optional] Separator to use between last two elements
     *                               - if `null` then `$separator` will be used. Default: `null`.
     * @return string
     */
    public static function implode(array $array, $separator = ',', $lastSeparator = null) {
        $lastSeparator = $lastSeparator === null ? $separator : $lastSeparator;
        $last = count($array) > 1 ? array_pop($array) : null;
        $string = implode($separator, $array);
        return $last ? $string . $lastSeparator . $last : $string;
    }

    /**
     * Implodes specific key values from an array collection.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::implodeByKey(array(
     *          array('id' => 3, 'title' => 'Lorem ipsum', 'categoryId' => 4),
     *          array('id' => 2, 'title' => 'Lipsum', 'categoryId' => 2),
     *          array('id' => 4, 'title' => 'Dolor sit amet', 'categoryId' => 3),
     *          array('id' => 6, 'title' => 'Adipiscit elit', 'categoryId' => 4),
     *          array('id' => 1, 'title' => 'Aequetam alitat', 'categoryId' => 3)
     *      ), 'id');
     *      // -> '3,2,4,6,1'
     *
     * @param array $array Array collection to implode.
     * @param string $key Implode values assigned to this key.
     * @param string $separator [optional] Separator to implode by. Default: `,`.
     * @return string
     */
    public static function implodeByKey(array $array, $key, $separator = ',') {
        return static::implode(static::pluck($array, $key), $separator);
    }

    /**
     * Searches an array collection for a value found under a key and returns
     * the top level key.
     *
     * Only returns key for the first occurence of the searched value.
     *
     * Strict comparison `===` is used when searching.
     *
     * Returns `false` if not found.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::search(array(
     *          array('id' => 3, 'title' => 'Lorem ipsum', 'categoryId' => 4),
     *          array('id' => 2, 'title' => 'Lipsum', 'categoryId' => 2),
     *          array('id' => 4, 'title' => 'Dolor sit amet', 'categoryId' => 3),
     *          array('id' => 6, 'title' => 'Adipiscit elit', 'categoryId' => 4),
     *          array('id' => 1, 'title' => 'Aequetam alitat', 'categoryId' => 3)
     *      ), 'categoryId', 3);
     *      // -> 2
     *
     * @param array $array Array collection to search through.
     * @param string $key Search through values of this key.
     * @param string $value Value to search for.
     * @return string|int|bool
     */
    public static function search(array $array, $key, $value) {
        foreach ($array as $k => &$row) {
            if ((isset($row[$key])) && ($row[$key] === $value)) {
                return $k;
            }
        }
        return false;
    }

    /**
     * Get the numerical index for an associative key.
     *
     * In other words: returns numerical position of a key in an associative array.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::keyPosition(array(
     *          'one' => 'frodo',
     *          'two' => 'sam',
     *          'three' => 'pippin',
     *          'four' => 'merry'
     *      ), 'three');
     *      // -> 2
     *
     * Returns `false` if the key doesn't exist.
     *
     * @param array $array Associative array to search through.
     * @param string $key Key name.
     * @return int|bool
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
     * Removes a key from all rows in an array collection.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::keyRemove(array(
     *          array('id' => 3, 'title' => 'Lorem ipsum', 'categoryId' => 4),
     *          array('id' => 2, 'title' => 'Lipsum', 'categoryId' => 2),
     *          array('id' => 4, 'title' => 'Dolor sit amet', 'categoryId' => 3),
     *          array('id' => 6, 'title' => 'Adipiscit elit', 'categoryId' => 4),
     *          array('id' => 1, 'title' => 'Aequetam alitat', 'categoryId' => 3)
     *      ), 'categoryId');
     *      // -> array('id' => 3, 'title' => 'Lorem ipsum'),
     *      // -> array('id' => 2, 'title' => 'Lipsum'),
     *      // -> array('id' => 4, 'title' => 'Dolor sit amet'),
     *      // -> array('id' => 6, 'title' => 'Adipiscit elit'),
     *      // -> array('id' => 1, 'title' => 'Aequetam alitat')
     *
     * @param array $array Array collection.
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
     * Adds a key to every row in an array collection.
     *
     * Optionally you can specify what value to be set under that key.
     *
     * It will **overwrite existing key**.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::keyAdd(array(
     *          array('id' => 3, 'title' => 'Lorem ipsum', 'categoryId' => 4),
     *          array('id' => 2, 'title' => 'Lipsum', 'categoryId' => 2),
     *          array('id' => 4, 'title' => 'Dolor sit amet', 'categoryId' => 3),
     *          array('id' => 6, 'title' => 'Adipiscit elit', 'categoryId' => 4),
     *          array('id' => 1, 'title' => 'Aequetam alitat', 'categoryId' => 3)
     *      ), 'featured', true);
     *      // array('id' => 3, 'title' => 'Lorem ipsum', 'categoryId' => 4, 'featured' => true),
     *      // array('id' => 2, 'title' => 'Lipsum', 'categoryId' => 2, 'featured' => true),
     *      // array('id' => 4, 'title' => 'Dolor sit amet', 'categoryId' => 3, 'featured' => true),
     *      // array('id' => 6, 'title' => 'Adipiscit elit', 'categoryId' => 4, 'featured' => true),
     *      // array('id' => 1, 'title' => 'Aequetam alitat', 'categoryId' => 3, 'featured' => true)
     *
     * @param array $array Array collection.
     * @param string $key Key to be added.
     * @param mixed $value [optional] Optional value to be set under this key. Default: `null`.
     * @return array
     */
    public static function keyAdd(array $array, $key, $value = null) {
        foreach($array as $k => &$row) {
            $row[$key] = $value;
        }

        return $array;
    }

    /**
     * Removes all `$values` from the `$array`.
     *
     * It doesn't change the array keys.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::removeValues(
     *          array('one', 'two', 'three', 'four'),
     *          array('two', 'three')
     *      );
     *      // -> array(0 => 'one', 3 => 'four')
     * 
     * @param  array  $array  Array to remove items from.
     * @param  array  $values Values to be removed.
     * @return array
     */
    public static function removeValues(array $array, array $values) {
        return array_diff($array, $values);
    }

    /**
     * Checks whether `$array` has at least one of the `$values`.
     *
     * Returns `false` if `$values` is empty.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::hasValue(array('one', 'two', 'three'), 'two');
     *      // -> true
     * 
     * @param  array   $array  Array to be checked.
     * @param  array   $values Values to be found.
     * @return boolean
     */
    public static function hasValue(array $array, array $values) {
        return count(array_intersect($array, $values)) > 0;
    }

    /**
     * Sorts an array collection by value of a row key.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::multiSort(array(
     *          array('id' => 3, 'title' => 'Lorem ipsum', 'categoryId' => 4),
     *          array('id' => 2, 'title' => 'Lipsum', 'categoryId' => 2),
     *          array('id' => 4, 'title' => 'Dolor sit amet', 'categoryId' => 3),
     *          array('id' => 6, 'title' => 'Adipiscit elit', 'categoryId' => 4),
     *          array('id' => 1, 'title' => 'Aequetam alitat', 'categoryId' => 3)
     *      ), 'id');
     *      // -> array('id' => 1, 'title' => 'Aequetam alitat', 'categoryId' => 3),
     *      // -> array('id' => 2, 'title' => 'Lipsum', 'categoryId' => 2),
     *      // -> array('id' => 3, 'title' => 'Lorem ipsum', 'categoryId' => 4),
     *      // -> array('id' => 4, 'title' => 'Dolor sit amet', 'categoryId' => 3),
     *      // -> array('id' => 6, 'title' => 'Adipiscit elit', 'categoryId' => 4)
     *
     * @param array $array Array collection to sort.
     * @param string $key Key to sort by.
     * @param bool $reverse [optional] `true` for descending order, `false` for ascending. Default: `false`.
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
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::sortPaths(array(
     *          'company/wizards/gandalf.txt',
     *          'company/dwarves/oin.txt',
     *          'company/dwarves/thorin.txt',
     *          'company/bilbo.txt',
     *          'company/dwarves/bifur.txt',
     *          'company/dwarves/bombur.txt',
     *          'company/wizards/radagast.txt'
     *      ));
     *      // -> array(
     *      //      'company/dwarves/bifur.txt',
     *      //      'company/dwarves/bombur.txt',
     *      //      'company/dwarves/oin.txt',
     *      //      'company/dwarves/thorin.txt',
     *      //      'company/wizards/gandalf.txt',
     *      //      'company/wizards/radagast.txt',
     *      //      'company/bilbo.txt'
     *      // );
     *
     * @param  array   $paths     Array of paths (strings).
     * @param  boolean $rootFirst [optional] Root first? Default: `false` (child first).
     * @param  boolean $separator [optional] Used path separator. Default: `DIRECTORY_SEPARATOR`.
     * @return array
     */
    public static function sortPaths(array $paths, $rootFirst = false, $separator = DIRECTORY_SEPARATOR) {
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
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::pushAfter(array(
     *          'one' => 'frodo',
     *          'two' => 'sam',
     *          'three' => 'pippin',
     *          'four' => 'merry'
     *      ), 'bill', 'two', 'mule');
     *      // -> array(
     *      //      'one' => 'frodo',
     *      //      'two' => 'sam',
     *      //      'mule' => 'bill',
     *      //      'three' => 'pippin',
     *      //      'four' => 'merry'
     *      // );
     *
     * @param array $array Array to push into.
     * @param mixed $input Value to push.
     * @param int|string $position Position or key after which to push the value.
     * @param string $key [optional] Specific key assigned to the value. Default: `null`.
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
     * Filters the array so that it contains only the keys from the `$allowedKeys` list.
     *
     * Ie. removes all keys that are not in `$allowedKeys` array.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::filterKeys(
     *          array('id' => 2, 'title' => 'Lipsum', 'deleted' => false, 'featured' => true, 'slug' => 'lipsum.html'),
     *          array('title', 'featured')
     *      );
     *      // -> array('title' => 'Lipsum', 'featured' => true)
     *
     * Useful for filtering POST-ed input.
     *
     * @param array $array Array to be filtered.
     * @param array $allowedKeys List of keys that are allowed in the `$array`.
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
     * Flattens an array, makes it 1-dimensional.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::flatten(array(
     *          array('id' => 2, 'title' => 'Lorem ipsum'),
     *          array('id' => 3, 'title' => 'Lipsum.com'),
     *          array('id' => 5, 'title' => 'Dolor sit amet')
     *      ));
     *      // -> array(2, 'Lorem ipsum', 3, 'Lipsum.com', 5, 'Dolor sit amet');
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
     * You can pass as many arrays as you want.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::merge(
     *          array('one' => 'frodo', 'two' => 'bilbo'),
     *          array('two' => 'sam'),
     *          array('mule' => 'bill'),
     *          array('three' => 'pippin', 'four' => 'merry')
     *      );
     *      // -> array('one' => 'frodo', 'two' => 'sam', 'mule' => 'bill', 'three' => 'pippin', 'four' => 'merry')
     * 
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
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::mergeDeep(array(
     *          'settings' => array(
     *              'login_enabled' => true,
     *              'users' => array('Jane', 'John')
     *          )
     *      ), array(
     *          'settings' => array(
     *              'logout_enabled' => false
     *              'users' => array('Doe')
     *          ),
     *          'config' => array()
     *      ));
     *      // -> array(
     *      //      'settings' => array(
     *      //          'login_enabled' => true,
     *      //          'users' => array('Jane', 'John', 'Doe'),
     *      //          'logout_enabled' => false
     *      //      ),
     *      //      'config' => array()
     *      // )
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
     * Joins items from the `$from` array collection into the `$into` collection based on matching criteria.
     * 
     * Default type of join is _outer_ meaning that if matching row wasn't found in the second collection
     * the row in the first collection will not be removed (this can be altered by setting
     * the `$type` argument to `ArrayUtils::JOIN_OUTER`).
     *
     * Example:
     *
     *      $posts = array(
     *          array('id' => 3, 'title' => 'Lorem ipsum', 'imageId' => 5),
     *          array('id' => 4, 'title' => 'Lipsum', 'imageId' => 6),
     *          array('id' => 5, 'title' => 'Muspi merol', 'imageId' => 5),
     *          array('id' => 6, 'title' => 'Dolor sit amet', imageId' => 34)
     *      );
     *      $images = array(
     *          array('id' => 5, 'url' => 'http://replygif.net/i/1452.gif'),
     *          array('id' => 6, 'url' => 'http://replygif.net/i/1448.gif')
     *      );
     *      echo \MD\Foundation\Utils\ArrayUtils::join($posts, $images, 'imageId', 'image', 'id');
     *      // -> array(
     *      // ->   array('id' => 3, 'title' => 'Lorem ipsum', 'imageId' => 5,
     *      // ->       'image' => array('id' => 5, 'url' => 'http://replygif.net/i/1452.gif')),
     *      // ->   array('id' => 4, 'title' => 'Lipsum', 'imageId' => 6,
     *      // ->       'image' => array('id' => 6, 'url' => 'http://replygif.net/i/1448.gif')),
     *      // ->   array('id' => 5, 'title' => 'Muspi merol', 'imageId' => 5,
     *      // ->       'image' => array('id' => 5, 'url' => 'http://replygif.net/i/1452.gif')),
     *      // ->   array('id' => 6, 'title' => 'Dolor sit amet', imageId' => 34)
     *      // -> )
     *
     * In the above example, if we added 6ths argument `ArrayUtils::JOIN_INNER` then the last `$posts` element (with `id = 6`)
     * would not be included in the `::join()` results.
     *
     * @param array $into Collection of data in which to put values of $from.
     * @param array $from Collection of data from which get values to put into $into.
     * @param string $onKey What key from $into to compare on?
     * @param string $intoKey Into what key to put values from $from.
     * @param string $fromKey What key from $from to compare on?
     * @param string $type [optional] Type of join. Can also be `ArrayUtils::JOIN_INNER` which will remove all items
     *                  from `$into` that don't have values in `$from`. Default: `ArrayUtils::JOIN_OUTER`.
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
     * Check whether the specified keys are set inside the given array and are not empty.
     *
     * Returns `true` if all is correct, `false` otherwise.
     *
     * String values are trimmed before checking, so ` ` (single space) is an invalid value.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::checkValues(array(
     *          'id' => 5,
     *          'title' => 'Lorem ipsum',
     *          'slug' => ''
     *      ), array('id', 'title', 'slug', 'author'));
     *      // -> false
     *
     * The above example fails because `slug` key is empty and there is no `author` key.
     *
     * @param array $array Array to check.
     * @param array $keys Array of keys to check.
     * @return bool
     */
    public static function checkValues(array $array, array $keys) {
        foreach($keys as $key) {
            if (
                (!isset($array[$key]))
                || (is_array($array[$key]) && empty($array[$key]))
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
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::cleanEmpty(array(
     *          'one' => 'frodo',
     *          'two' => 'sam',
     *          'three' => '',
     *          'four' => null,
     *          'five' => 0,
     *          'six' => false
     *      ));
     *      // -> array('one' => 'frodo', 'two' => 'sam', 'five' => 0, 'six' => false)
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
     * Parses an array into a HTTP query string.
     *
     * The query string will not include the initial `?` sign, so it can be appended
     * to an existing query string.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::toQueryString(array(
     *          'key' => 'val',
     *          'key1' => array(
     *              'subkey' => 'subval',
     *              'subkey1' => 'subval1'
     *          ),
     *          'key2' => array('a', 'b', 'c', 'd')
     *      ));
     *      // -> key=val&key1%5Bsubkey%5D=subval&key1%5Bsubkey1%5D=subval1&key2%5B0%5D=a&key2%5B1%5D=b&key2%5B2%5D=c&key2%5B3%5D=d
     *
     * @param array $array Array to be converted to query string.
     * @return string
     */
    public static function toQueryString(array $array) {
        return http_build_query($array);
    }

    /**
     * Converts the given array to an object.
     * 
     * The conversion is "deep", ie. all dimensions will be converted.
     *
     * By default it creates an object of `\StdClass`, but you can pass your own object
     * as the 2nd argument. The array values will be set as public properties on the object.
     *
     * @param array $array Array to convert to object.
     * @param object $object [optional] Object to which assign the properties, usually for
     *                       internal use. Default: `null`.
     * @return object
     */
    public static function toObject(array $array, $object = null) {
        if ($object !== null && !is_object($object)) {
            throw new InvalidArgumentException('object', $object, 2);
        }

        $parent = ($object !== null) ? $object : new StdClass();

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
     * Creates an array from a given object
     * 
     * The conversion is "deep", ie. all dimensions will be converted.
     *
     * @param object|array $object Object to be converted to an array or an array of objects (collection).
     * @param array $parent [optional] Usually for internal use of the function. A reference to parent array. Default: `array()`.
     * @param array $keys [optional] If you don't want the whole object converted to array, specify the names of keys
     *                    that you are interested in. Default: `array()`.
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
     * Flatten a multi-dimensional associative array to dot notation.
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::dot(array(
     *          'flat' => 'bar',
     *          'foo' => array(
     *              'bar' => array(
     *                  'baz' => true,
     *                  'bat' => false
     *              )
     *          )
     *      ));
     *      // -> array('flat' => 'bar', 'foo.bar.baz' => true, 'foo.bar.bat' => false)
     *
     * @param  array   $array Array to be converted to dot notation.
     * @param  string  $prefix [optional] Prepend this string to all keys. Default: `null`.
     * @return array
     */
    public static function dot(array $array, $prefix = null) {
        $results = array();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, static::dot($value, $prefix . $key .'.'));
            } else {
                $results[$prefix . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * Returns value from the key at given path (using dot notation).
     *
     * Example:
     *
     *      echo \MD\Foundation\Utils\ArrayUtils::get(array(
     *          'flat' => 'bar',
     *          'foo' => array(
     *              'bar' => array(
     *                  'baz' => true,
     *                  'bat' => false
     *              )
     *          )
     *      ), 'foo.bar.baz');
     *      // -> true
     *
     * @param  array  $array Array to get the value from.
     * @param  string $key Key or path to key.
     * @param  mixed  $default [optional] Default value if the key was not found. Default: `null`.
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

<?php
/**
 * A set of string utility functions.
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
use MD\Foundation\Utils\ObjectUtils;

/**
 * @static
 */
class StringUtils
{

    /**
     * Accented letters map used in StringUtils::translit() method.
     * 
     * @var array
     */
    protected static $_accentLettersMap = array(
        'A' => array('Á','À','Â','Ǎ','Ă','Ã','Ả','Ạ','Ä','Å','Ā','Ą','Ấ','Ầ','Ẫ','Ẩ','Ậ','Ắ','Ằ','Ẵ','Ẳ','Ặ','Ǻ'),
        'a' => array('á','à','â','ǎ','ă','ã','ả','ạ','ä','å','ā','ą','ấ','ầ','ẫ','ẩ','ậ','ắ','ằ','ẵ','ẳ','ặ','ǻ'),
        'C' => array('Ć','Ĉ','Č','Ċ','Ç'),
        'c' => array('ć','ĉ','č','ċ','ç'),
        'D' => array('Ď','Đ','Ð'),
        'd' => array('ď','đ'),
        'E' => array('É','È','Ê','Ě','Ĕ','Ẽ','Ẻ','Ė','Ë','Ē','Ę','Ế','Ề','Ễ','Ể','Ẹ','Ệ'),
        'e' => array('é','è','ê','ě','ĕ','ẽ','ẻ','ė','ë','ē','ę','ế','ề','ễ','ể','ẹ','ệ'),
        'G' => array('Ğ','Ĝ','Ġ','Ģ'),
        'g' => array('ğ','ĝ','ġ','ģ'),
        'H' => array('Ĥ','Ħ'),
        'h' => array('ĥ','ħ'),
        'I' => array('Í','Ì','Ĭ','Î','Ǐ','Ï','Ĩ','Į','Ī','Ỉ','Ị'),
        'i' => array('í','ì','ĭ','î','ǐ','ï','ĩ','į','ī','ỉ','ị'),
        'J' => array('Ĵ'),
        'j' => array('ĵ'),
        'K' => array('Ķ'),
        'k' => array('ķ'),
        'L' => array('Ĺ','Ľ','Ļ','Ł','Ŀ'),
        'l' => array('ĺ','ľ','ļ','ł','ŀ'),
        'N' => array('Ń','Ň','Ñ','Ņ'),
        'n' => array('ń','ň','ñ','ņ'),
        'O' => array('Ó','Ò','Ŏ','Ô','Ố','Ồ','Ỗ','Ổ','Ǒ','Ö','Ő','Õ','Ø','Ǿ','Ō','Ỏ','Ơ','Ớ','Ờ','Ỡ','Ở','Ợ','Ọ','Ộ'),
        'o' => array('ó','ò','ŏ','ô','ố','ồ','ỗ','ổ','ǒ','ö','ő','õ','ø','ǿ','ō','ỏ','ơ','ớ','ờ','ỡ','ở','ợ','ọ','ộ'),
        'P' => array('Ṕ','Ṗ'),
        'p' => array('ṕ','ṗ'),
        'R' => array('Ŕ','Ř','Ŗ'),
        'r' => array('ŕ','ř','ŗ'),
        'S' => array('Ś','Ŝ','Š','Ş'),
        's' => array('ś','ŝ','š','ş'),
        'T' => array('Ť','Ţ','Ŧ'),
        't' => array('ť','ţ','ŧ'),
        'U' => array('Ú','Ù','Ŭ','Û','Ǔ','Ů','Ü','Ǘ','Ǜ','Ǚ','Ǖ','Ű','Ũ','Ų','Ū','Ủ','Ư','Ứ','Ừ','Ữ','Ử','Ự','Ụ'),
        'u' => array('ú','ù','ŭ','û','ǔ','ů','ü','ǘ','ǜ','ǚ','ǖ','ű','ũ','ų','ū','ủ','ư','ứ','ừ','ữ','ử','ự','ụ'),
        'W' => array('Ẃ','Ẁ','Ŵ','Ẅ'),
        'w' => array('ẃ','ẁ','ŵ','ẅ'),
        'Y' => array('Ý','Ỳ','Ŷ','Ÿ','Ỹ','Ỷ','Ỵ'),
        'y' => array('ý','ỳ','ŷ','ÿ','ỹ','ỷ','ỵ'),
        'Z' => array('Ź','Ž','Ż'),
        'z' => array('ź','ž','ż'),
        'ss' => array('ß')
    );
    
    /**
     * Truncates a string to a specific length.
     * 
     * @param string $text String to truncate.
     * @param int $limit [optional] Maximum length of the string. Default is 72.
     * @param string $add [optional] String to append at the end. Default is '...'
     * @return string
     */
    public static function truncate($text, $limit = 72, $add = '...') {
        if (!is_string($text)) {
            throw new InvalidArgumentException('string', $text);
        }

        if (empty($text)) {
            return $text;
        }

        $limit = intval($limit);
        if (strlen($text) <= $limit) {
            return $text;
        }

        $suffixLength = strlen($add);
        if ($limit - $suffixLength < 0) {
            return substr($add, 0, $limit);
        }
        
        $text = substr($text, 0, $limit - $suffixLength); // crop the string to a given limit minus suffix

        $lastSpacePos = strrpos($text, ' ');
        if ($lastSpacePos) {
            $text = substr($text, 0, strrpos($text, ' ')); // find the last occurrence of a space and crop the string to it
        }
                
        $text = rtrim($text, '.!?:;,-'); // remove unwanted punctuation from the end of the string
        
        return $text . $add;
    }
    
    /**
     * Prefixes the given string with 0's until its length is what is given in the 2nd argument.
     * 
     * @param string $string String to be prefixed with 0's.
     * @param int $maxLength [optional] Target length of the string. Default: 4.
     * @return string
     */
    public static function zeroFill($string, $maxLength = 4) {
        $sPrintfString = '%0'. intval($maxLength) . 's';
        return sprintf($sPrintfString, $string);
    }

    /**
     * Clears the given string of any white spaces, punctuation, new lines, tabs, etc.
     * 
     * @param string $string
     * @param bool $punctuation [optional] Remove punctuation? Default: true.
     * @return string
     */
    public static function clear($string, $punctuation = true) {
        $string = trim($string);
        $string = strip_tags($string);

        if ($punctuation) {
            $string = str_replace(array(
                '`', '~', '!', '@', '#', '$', '%', '^', '*', '(', ')', '-', '_', '+', '=', '[', '{', '}', ']', '|', "\\", ':', ';', '"', "'", '<', '>', ',', '.', '?', '/'
            ), '', $string);
        }

        $string = str_replace(array("\n", "\r", "\n\r"), ' ', $string);
        $string = str_replace(NL, ' ', $string);
        $string = str_replace(TAB, ' ', $string);

        $string = preg_replace('/\s+/', ' ', $string);
        return $string;
    }

    /**
     * Returns an array of all words (not unique) in the given string.
     * 
     * @param string $string
     * @return array
     */
    public static function getWords($string, $removePunctuation = false) {
        $string = static::clear($string, $removePunctuation);
        
        if (empty($string)) {
            return array();
        }

        $words = mb_split(' ', $string);
        return $words;
    }
    
    /**
     * Return the first word found in the given string.
     * 
     * @param string $string
     * @return string
     */
    public static function getFirstWord($string) {
        $words = static::getWords($string, true);
        return (!empty($words)) ? $words[0] : null;
    }

    /**
     * Returns the first sentence from the given string, previously removing any HTML tags.
     * 
     * @param  string $string
     * @return string
     */
    public static function getFirstSentence($string) {
        $string = strip_tags($string);
        $breakers = array('.', '!', '?');
        $breakerPosition = mb_strlen($string);
        foreach($breakers as $breaker) {
            $position = stripos($string, $breaker .' ');
            if ($position) {
                $breakerPosition = min($breakerPosition, $position);
            }
        }
        return mb_substr($string, 0, $breakerPosition);
    }
    
    /**
     * Returns how many words are used in the string.
     * 
     * @param string $string
     * @return int
     */
    public static function wordCount($string) {
        $string = static::translit($string);
        $words = static::getWords($string);
        return count($words);
    }

    /**
     * Tries to remove any accents from letters in the given string by replacing them with similar looking letters.
     * 
     * Experimental. Use only with UTF-8 strings.
     * 
     * @param string $string String to be translit.
     * @param string $encoding [optional] If you know the string's encoding then put it here. Default: 'UTF-8'.
     * @return string
     */
    public static function translit($string, $encoding = 'UTF-8') {
        foreach(static::$_accentLettersMap as $ch => $accents) {
            $string = str_replace($accents, $ch, $string);
        }

        $string = iconv($encoding, 'ISO-8859-1//TRANSLIT', $string);
        return $string;
    }
    
    /**
     * Make a string that is URL (SEO) friendly.
     * 
     * Note that it doesn't mean it makes a valid URL - it will escape normally URL accepted characters like %, ?, / or &.
     * 
     * @param string $string String to make URL friendly.
     * @param bool $lowercase [optional] Should the string be made lowercase? Default: true.
     * @return string
     */
    public static function urlFriendly($string, $lowercase = true) {
        $string = static::translit($string);
        $string = utf8_decode($string);
        $string = htmlentities($string);
        $string = ($lowercase) ? strtolower($string) : $string;
        $string = str_replace('&amp;', 'and', $string);
        $string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $string);
        $string = preg_replace("/([^a-zA-Z0-9]+)/", "-", html_entity_decode($string));
        $string = trim($string, "-");
        return $string;
    }

    /**
     * Make a string that is file system friendly.
     * 
     * @param string $string String to make file system friendly.
     * @return string
     */
    public static function fileNameFriendly($string) {
        $string = static::translit($string);
        $string = utf8_decode($string);
        $string = htmlentities($string);
        $string = str_replace('&amp;', 'and', $string);
        $string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $string);
        $string = preg_replace("/([^a-zA-Z0-9_\.\(\)]+)/", "-", html_entity_decode($string));
        $string = trim($string, "-");
        return $string;
    }

    /**
     * Convert a string with words separated by a separator to camelCase.
     * 
     * @param string $string
     * @param string $separator [optional] Separator to use to split the string. Default: '-'.
     * @return string
     */
    public static function toCamelCase($string, $separator = '-') {
        $string = preg_replace_callback('/'. $separator .'([a-z])/i', function($matches) {
            return strtoupper($matches[1]);
        }, $string);
        return $string;
    }

    /**
     * Convert a string in camelCase to hyphens, e.g. uploadFile to upload-file.
     * 
     * @param string $string
     * @return string
     * 
     * @deprecated Still here for legacy. Use StringUtils::toSeparated() instead.
     */
    public static function toHyphenated($string) {
        return static::toSeparated($string, '-');
    }

    /**
     * Convert a string in camelCase to separated by the given separator.
     * 
     * @param string $string
     * @param string $separator [optional] Separator to use. Default: '-'.
     * @return string
     */
    public static function toSeparated($string, $separator = '-') {
        $string = preg_replace('/([a-z])([A-Z])/', '$1'. $separator .'$2', $string);
        $string = strtolower($string);
        return $string;
    }
    
    /**
     * Checks whether a string is a valid e-mail address.
     * 
     * @param string $email String to validate.
     * @return bool
     */
    public static function isEmail($email) {
        return (preg_match('/[a-z0-9\.-]+@[a-z0-9.-]+\.[a-z]{2,4}/i', $email) === 1);
    }
    
    /**
     * Checks whether a string is a valid URL.
     * 
     * @param string $url String to validate.
     * @return bool True if valid URL, false if not.
     */
    public static function isUrl($url) {
        return (preg_match('|^http(s)?://[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url) === 1);
    }

    /**
     * Checks if the given string is a valid class name.
     * 
     * @param string $name Class name to validate.
     * @param bool $allowNamespace [optional] Can the class name include a namespace? Default: false.
     * @return bool
     */
    public static function isClassName($name, $allowNamespace = false) {
        if (empty($name)) {
            return false;
        }

        if ($allowNamespace) {
            $name = explode(NS, $name);
            $allow = true;
            foreach($name as $className) {
                if (!static::isClassName($className, false)) {
                    $allow = false;
                    break;
                }
            }

            return $allow;
        }

        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/i', $name)) {
            return false;
        }

        return true;
    }
    
    /**
     * Fix a given URL if it doesn't have http:// in front (common mistake! :))
     * 
     * @param string $url URL to check or fix.
     * @return string
     */
    public static function fixUrlProtocol($url) {
        return (stripos($url, 'http://') === 0 || stripos($url, 'https://') === 0) ? $url : 'http://'. $url;
    }
    
    /**
     * Generate a random string of a given length.
     * 
     * @param int $length [optional] Length of the string. Default: 16
     * @param bool $capitals [optional] Should the string include capital letters? Default: true
     * @param bool $punctuation [optional] Should the string include special characters like punctuation? Default: false
     * @return string
     */
    public static function random($length = 16, $capitals = true, $punctuation = false) {
        $chars = '1234567890abcdefghijkmnopqrstuvwxyz';
        
        if ($capitals) {
            $chars .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }

        if ($punctuation) {
            $chars .= '?!.,;:^#@&';
        }
        
        $string = '';
        for ($i = 1; $i <= $length; $i++) {
            $string .= $chars{mt_rand(0, strlen($chars) - 1)};
        }

        return $string;
    }
    
    /**
     * Parses the given string looking for variables to insert to from the given set of variables.
     * 
     * Ie. Looks for occurrences of variables like {foo} or {bar} and replaces them with values found under
     * keys 'foo' or 'bar' (respectively) in the given array of variables.
     *
     * @param string $string String to parse.
     * @param mixed $variables Either an array or an object with variables.
     * @return string
     */
    public static function parseVariables($string, $variables) {
        if (!is_object($variables) && !is_array($variables)) {
            throw new InvalidArgumentException('array or object', $variables);
        }

        $string = preg_replace_callback('/{([\w\d_\.]+)}/is', function($matches) use ($variables) {
            $var = $matches[1];
            $value = '';

            if (is_object($variables)) {
                $getter = ObjectUtils::getter($var);
                if (method_exists($variables, $getter)) {
                    $value = strval(call_user_func(array($variables, $getter)));
                } elseif (isset($variables->$var)) {
                    $value = strval($variables->$var);
                }
            } elseif (is_array($variables)) {
                if (isset($variables[$var])) {
                    $value = strval($variables[$var]);
                }
            }

            return $value;
        }, $string);
        
        return $string;
    }
    
    /**
     * Changes the given number of seconds to a format hh:mm:ss.
     * 
     * @param int $seconds Number of seconds to be transformed.
     * @param bool $hideHoursWhenZero [optional] If the time returned has zero hours then do not display them. Default: false.
     * @return string
     */
    public static function secondsToTimeString($seconds, $hideHoursWhenZero = false) {
        $seconds = intval($seconds);
        
        // add negative sign but count as positive for negative values
        $negative = '';
        if ($seconds < 0) {
            $seconds = $seconds * (-1);
            $negative = '-';
        }
        
        $hours = floor($seconds / (60 * 60));
        $seconds = $seconds - $hours * 60 * 60;
        
        $minutes = floor($seconds / 60);
        $seconds = $seconds - $minutes * 60;
        
        $seconds = round($seconds);

        $timeString = '';

        if ($hours != 0 || !$hideHoursWhenZero) {
            $timeString = $hours .':';
        }

        $timeString .= static::zeroFill($minutes, 2) .':';
        $timeString .= static::zeroFill($seconds, 2);
        
        return $negative . $timeString;
    }
    
    /**
     * Changes string of time format hh:mm:ss to seconds as integer. Reverse of StringUtils::secondsToTimeString().
     * 
     * Can also take '.' (dot) as a separator.
     *
     * @param string $string
     * @return int
     */
    public static function timeStringToSeconds($string) {
        $seconds = 0;
        $multipliers = array(1, 60, 3600);
        $negative = (substr($string, 0, 1) === '-') ? true : false;
        $string = ltrim($string, '-');
        
        $char = (strpos($string, ':') !== false) ? ':' : '.';
        $times = explode($char, $string);
        // 0 => seconds, 1 => minutes, 2 => hours
        $times = array_reverse($times);
        
        foreach($times as $i => $number) {
            $number = intval(ltrim($number, '0')); // remove suffixed 0's and make int
            $seconds = $seconds + ($number * $multipliers[$i]);
        }

        if ($negative) {
            $seconds = $seconds * -1;
        }
        
        return $seconds;
    }

    /**
     * Changes the given bytes to a user friendly string.
     * 
     * @param int $bytes
     * @return string
     */
    public static function bytesToString($bytes) {
        $bytes = intval($bytes);
        if ($bytes < 1024) {
            return $bytes .' b';
        }

        $kb = $bytes / 1024;
        if ($kb < 1024) {
            return number_format($kb, 0) .' kb';
        }

        $mb = $kb / 1024;
        if ($mb < 1024) {
            return number_format($mb, 1) .' MB';
        }

        $gb = $mb / 1024;
        return number_format($gb, 2) .' GB';
    }
    
    /**
     * Changes the given UNIX timestamp to a string saying 'xxx ago'.
     * 
     * @param int $timestamp UNIX timestamp.
     * @param int $levels [optional] How many levels of time periods to show.
     * @param mixed $returnDateIfOlder [optional] If specified then the function will return a regular date
     *                                 instead of 'xxx ago' if the date is older than this.
     *                                 Use (bool) false if you want to ommit this functionality.
     *                                 To use it pass any string that can be used by strtotime(). Default: '3 weeks ago'.
     * @param string $returnDateFormat [optional] If $returnDateIfOlder is set then this is the format in which
     *                                 the given date will be returned. Parsing of this will be rerouted to date()
     *                                 and this is the format that will be passed to that function. Default: 'd.m.Y H:i'.
     * @param bool $secondSpecific [optional] Should the return be specific to a second? Otherwise will return
     *                             'few seconds ago'. Defualt: false.
     * @param bool $trimAgo [optional] Should the 'ago' appendix be not added? Mainly for internal use. Default: false.
     * @return string
     */
    public static function timeAgo($timestamp, $levels = 1, $returnDateIfOlder = '3 weeks ago', $returnDateFormat = 'd.m.Y H:i', $secondSpecific = false, $trimAgo = false) {
        // if it only happened less than a minute ago then show 'few seconds ago'
        if (!$secondSpecific && $timestamp > strtotime('1 minute ago')) {
            return 'few seconds'. (!$trimAgo ? ' ago' : '');
        }

        // if it happened too far in the past then return regular date.
        if ($returnDateIfOlder && ($timestamp < strtotime($returnDateIfOlder))) {
            return date($returnDateFormat, $timestamp);
        }

        $currentTime = time();
        $levels--;
        $difference = $currentTime - $timestamp;

        $periods = array(
            array(
                'single' => 'second',
                'plural' => 'seconds'
            ),
            array(
                'single' => 'minute',
                'plural' => 'minutes'
            ),
            array(
                'single' => 'hour',
                'plural' => 'hours'
            ),
            array(
                'single' => 'day',
                'plural' => 'days'
            ),
            array(
                'single' => 'week',
                'plural' => 'weeks'
            ),
            array(
                'single' => 'month',
                'plural' => 'months'
            ),
            array(
                'single' => 'year',
                'plural' => 'years'
            ),
            array(
                'single' => 'decade',
                'plural' => 'decades'
            )
        );
                
        $lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

        // Determine which period we should use, based on the number of seconds lapsed.
        // If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
        // Go from decades backwards to seconds
        for ($val = count($lengths) - 1; $val >= 0; $val--) {
            $number = $difference / $lengths[$val];
            if ($number >= 1) {
                break;
            }
        }
        /*
        for (
            $val = count($lengths) - 1;
            ($val >= 0) && (($number = $difference / $lengths[$val]) < 1);
            $val--
        );*/

        $val = ($val < 0) ? 0 : $val;
        $number = floor($number);

        // Determine the minor value, to recurse through
        $newTime = $currentTime - ($difference % $lengths[$val]);

        // Return text
        $text = sprintf("%d %s ", $number, $number != 1 ? $periods[$val]['plural'] : $periods[$val]['single']);

        // Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
        if (($val >= 1) && (($currentTime - $newTime) > 0) && ($levels > 0)){
            $text .= static::timeAgo($newTime, $levels, $returnDateIfOlder, $returnDateFormat, true, true);
        }
         
        return trim($text) . (!$trimAgo ? ' ago' : '');
    }
    
    /**
     * Strips HTML tags and encodes HTML special chars from a string.
     * 
     * @param string $string String to be stripped of HTML.
     * @return string
     */
    public static function stripHtml($string) {
        $string = strip_tags($string);
        $string = htmlspecialchars($string);
        return $string;
    }
    
    /**
     * Explodes the given string just like php's explode() function with additional possibility of multiple exploding delimeters.
     *
     * @param string $standardDelimeter Standard delimeter by which explode the string.
     * @param string $string String to explode.
     * @param array $delimeters [optional] Array of any other delimeters to take into account while exploding.
     * @return array
     */
    public static function multiExplode($standardDelimeter, $string, $delimeters = array()) {
        foreach($delimeters as $delimeter) {
            $string = str_replace($delimeter, $standardDelimeter, $string);
        }
        
        $array = explode($standardDelimeter, $string);
        return $array;
    }

    /**
     * Searches the given string for occurences of another string. Returns true if found, false if not.
     * 
     * Case insensitive. Can search for multiple strings in one call.
     * 
     * @param string $string String to be searched in.
     * @param string|array $search String or an array of strings to search for.
     * @return bool
     */
    public static function search($string, $search) {
        $needles = is_array($search) ? $search : array($search);

        foreach($needles as &$needle) {
            if  (stripos($string, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
    
}
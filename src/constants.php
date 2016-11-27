<?php
/**
 * Few useful global constants used by Foundation.
 * 
 * @package Foundation
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */

// convenience constants
defined('TAB') || define('TAB', "\t");
defined('NL') || define('NL', "\n");
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PS') || define('PS', PATH_SEPARATOR);
defined('NS') || define('NS', '\\'); // namespace separator

// compatibility constants, as they might be missing on some systems (e.g. Alpine Linux)
defined('GLOB_BRACE') || define('GLOB_BRACE', 0);

<?php
/**
 * @package Foundation
 * @subpackage Exceptions
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation\Exceptions;

use MD\Foundation\Exceptions\Exception;

/**
 * Exception usually thrown when trying to overwrite something that is read only.
 */
class ReadOnlyException extends Exception
{



}
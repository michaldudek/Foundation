<?php
/**
 * @package Foundation
 * @subpackage Debug
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation\Debug\Interfaces;

/**
 * Interface that allows the Debugger to easily dump clean objects.
 */
interface Dumpable
{

    /**
     * Returns the object in a form of printable array.
     * 
     * @return array
     */
    public function toDumpableArray();

}
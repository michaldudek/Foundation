<?php
/**
 * @package Foundation
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2013, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation;

use MD\Foundation\Debug\Interfaces\Dumpable;
use MD\Foundation\Utils\ObjectUtils;
use MD\Foundation\Utils\StringUtils;

/**
 * Extending this class will automatically add magic functionalities to your objects.
 *
 * It creates automatic getters and setters for any property you set on it.
 *
 * This is useful for when you want to quickly create an object and store some data in it,
 * but don't know what data it will be until runtime.
 *
 * It also adds two public methods `->__getClass()` and `::__class()` which return the name
 * of the class of the instantiated object.
 *
 * Example:
 *
 *      $object = new \MD\Foundation\MagicObject();
 *      $object->setId(2);
 *      echo $object->getId();
 *      // -> 2
 *      echo $object->id;
 *      // -> 2
 *      $object->id = 4;
 *      echo $object->getId();
 *      // -> 4
 *
 *      echo isset($object->id);
 *      // -> true
 *      echo isset($object->title);
 *      // -> false
 *
 *      #################
 *
 *      class MyClass extends \MD\Foundation\MagicObject {}
 *      $myObject = new MyClass();
 *
 *      echo $myObject->__getClass();
 *      // -> MyClass
 *
 *      echo MyClass::__class();
 *      // -> MyClass
 */
class MagicObject implements Dumpable
{

    /**
     * Container for all magic properties.
     * 
     * @var array
     */
    protected $__properties = array();

    /*****************************************************
     * MAGIC METHOD OVERLOADING
     *****************************************************/
    /**
     * Set a property.
     * 
     * @param string $property Property name.
     * @param mixed $value Value of the property.
     */
    final protected function __setProperty($property, $value) {
        if (!is_string($property)) {
            return trigger_error('Function '. get_called_class() .'::__setProperty() requires argument 1 to be a string, '. gettype($property) .' given.', E_USER_ERROR);
        }

        $this->__properties[$property] = $value;
    }

    /**
     * Get a property.
     * 
     * @param string $property Property name.
     * @return mixed
     */
    final protected function __getProperty($property) {
        if (!is_string($property)) {
            return trigger_error('Function '. get_called_class() .'::__getProperty() requires argument 1 to be a string, '. gettype($property) .' given.', E_USER_ERROR);
        }

        return isset($this->__properties[$property]) ? $this->__properties[$property] : null;
    }

    /**
     * Set a property. It will try to call a defined setter first.
     *
     * The called setter function will be camelcased version of the `$property`, e.g.
     *
     *      use MD\Foundation\MagicUtils;
     *
     *      class MyClass extends MagicUtils
     *      {
     *          protected $full_title;
     *
     *          public function setFullTitle($full_title)
     *          {
     *              $this->full_title = $full_title;
     *              echo 'Set full title to "'. $full_title .'"';
     *          }
     *      }
     *
     *      $object = new MyClass();
     *      $object->full_title = 'Lorem ipsum dolor sit amet.';
     *      // -> 'Set full title to "Lorem ipsum dolor sit amet."'
     *
     * If the property does not exist then it will be created.
     * 
     * @param string $property Name of the property.
     * @param mixed $value Value to set to.
     */
    final public function __set($property, $value) {
        // try to call a defined setter if it exists
        $setter = ObjectUtils::setter($property);
        if (method_exists($this, $setter)) {
            call_user_func(array($this, $setter), $value);
            return;
        }
        
        $this->__setProperty($property, $value);
    }
    
    /**
     * Get the property. It will try to call a defined getter first.
     * 
     * If the property does not exist then it will trigger an `E_USER_NOTICE`.
     *
     *      use MD\Foundation\MagicUtils;
     *
     *      class MyClass extends MagicUtils
     *      {
     *          protected $full_title = 'Lorem ipsum dolor sit amet.';
     *
     *          public function getFullTitle()
     *          {
     *              echo 'Getting full title "'. $this->full_title .'"';
     *              return $this->full_title;
     *          }
     *      }
     *
     *      $object = new MyClass();
     *      echo $object->full_title;
     *      // -> 'Getting full title "Lorem ipsum dolor sit amet."'
     * 
     * @param string $property Name of the property.
     * @return mixed
     */
    final public function __get($property) {
        // try to call a defined getter if it exists
        $getter = ObjectUtils::getter($property);
        if (method_exists($this, $getter)) {
            return call_user_func(array($this, $getter));
        }
        
        // if no getter then simply return the property if it exists
        if (array_key_exists($property, $this->__properties)) {
            return $this->__properties[$property];
        }
        
        // trigger a user notice if property not found
        return trigger_error('Call to undefined object property '. get_called_class() .'::$'. $property .'.', E_USER_NOTICE);
    }
    
    /**
     * Is the given property set?
     *
     * Example:
     *
     *      $object = new MD\Foundation\MagicObject();
     *      echo isset($object->title);
     *      // -> false
     * 
     * @param string $property Name of the property.
     * @return boolean
     */
    final public function __isset($property) {
        if (property_exists($this, $property)) {
            return isset($this->$property);
        }

        return isset($this->__properties[$property]);
    }
    
    /**
     * Unset the given property.
     *
     * Example:
     *
     *      $object = new MD\Foundation\MagicObject();
     *      $object->title = 'Lorem ipsum';
     *      echo $object->title;
     *      // -> 'Lorem ipsum'
     *
     *      unset($object->title);
     *      echo $object->title;
     *      // -> null
     * 
     * @param string $property Name of the property.
     */
    final public function __unset($property) {
        if (property_exists($this, $property)) {
            unset($this->$property);
            return;
        }

        unset($this->__properties[$property]);
    }
    
    /**
     * Overload setters and getters and do what they would normally do.
     *
     * This enables "automatic"/"magic" setters and getters on an object.
     *
     *      $object = new MD\Foundation\MagicObject();
     *      $object->setTitle('Lorem ipsum');
     *      echo $object->getTitle();
     *      // -> 'Lorem ipsum'
     *
     *      $object->setSomeRandomAnotherProperty(true);
     *      echo $object->getSomeRandomAnotherProperty();
     *      // -> true
     *
     * It also creates "issers" that always return `boolean` values.
     *
     *      $object = new MD\Foundation\MagicObject();
     *      $object->setEnabled(true);
     *      echo $object->isEnabled();
     *      // -> true
     *
     * If there is no property for the called getter then it will return `null`.
     * 
     * @param string $method Method name.
     * @param array $arguments Array of arguments.
     * @return mixed|null
     */
    final public function __call($method, $arguments) {
        $type = strtolower(substr($method, 0, 3));
        
        // called a setter or a getter ?
        if ($type === 'set' || $type === 'get') {
            $property = lcfirst(substr($method, 3));

            // decide on property name by checking if a camelCase exists first
            // and if not try the under_scored
            $property = (isset($this->__properties[$property]))
                ? $property
                : StringUtils::toSeparated($property, '_');

            if ($type === 'set') {
                // if a setter then require at least one argument
                if (!isset($arguments[0])) {
                    return trigger_error('Function '. get_called_class() .'::'. $method .'()" requires one argument, none given.', E_USER_ERROR);
                }

                return $this->__setProperty($property, $arguments[0]);
            } else if ($type === 'get') {
                return $this->__getProperty($property);
            }
        // @codeCoverageIgnoreStart
        }
        // @codeCoverageIgnoreEnd

        // called an isser?
        if (strtolower(substr($method, 0, 2)) === 'is') {
            $property = lcfirst(substr($method, 2));
            $property = (isset($this->__properties[$property]))
                ? $property
                : StringUtils::toSeparated($property, '_');

            $value = $this->__getProperty($property);
            // cast '0' as false
            return (!$value || $value == '0') ? false : true;
        }
    
        // undefined method called!
        return trigger_error('Call to undefined method '. get_called_class() .'::'. $method .'().', E_USER_ERROR);
    }

    /*****************************************************
     * OBJECT CLASS IDENTIFICATION
     *****************************************************/
    /**
     * Returns full name of the class of this object.
     *
     * Example:
     *
     *      class MyClass extends \MD\Foundation\MagicObject {}
     *      $myObject = new MyClass();
     *
     *      echo $myObject->__getClass();
     *      // -> MyClass
     * 
     * @return string
     */
    final public function __getClass() {
        return get_class($this);
    }

    /**
     * Returns full name of the class.
     *
     * Example:
     *
     *      class MyClass extends \MD\Foundation\MagicObject {}
     *      echo MyClass::__class();
     *      // -> MyClass
     *
     * This is the same as calling PHP 5.5 `MyClass::class`.
     * 
     * @return string
     */
    final public static function __class() {
        return get_called_class();
    }

    /*****************************************************
     * DUMPABLE INTERFACE
     *****************************************************/
    /**
     * Returns magic properties of the object.
     *
     * Example:
     *
     *      $object = new MD\Foundation\MagicObject();
     *      $object->setId(5);
     *      $object->setTitle('Lorem ipsum');
     *
     *      echo $object->toDumpableArray();
     *      // array('id' => 5, 'title' => 'Lorem ipsum')
     *
     * This method realizes `MD\Foundation\Debug\Interfaces\Dumpable` interface,
     * making it possible to display all properties of this object when dumping
     * it using `\MD\dump()` function.
     * 
     * @return array
     */
    public function toDumpableArray() {
        return $this->__properties;
    }

}
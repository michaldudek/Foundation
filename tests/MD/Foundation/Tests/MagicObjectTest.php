<?php
namespace MD\Foundation\Tests;

use ReflectionClass;

use MD\Foundation\MagicObject;

use MD\Foundation\Tests\TestFixtures\MagicObjectClass;

class MagicObjectTest extends \PHPUnit_Framework_TestCase
{

    protected $reflections = array();

    public function setUp() {
        $class = new ReflectionClass('MD\Foundation\MagicObject');
        foreach(array('__setProperty', '__getProperty') as $methodName) {
            $method = $class->getMethod($methodName);
            $method->setAccessible(true);
            $this->reflections[$methodName] = $method;
        }

        $properties = $class->getProperty('__properties');
        $properties->setAccessible(true);
        $this->reflections['__properties'] = $properties;
    }

    public function test_SetProperty() {
        $obj = new MagicObject();
        $this->assertInternalType('array', $this->reflections['__properties']->getValue($obj));

        $this->reflections['__setProperty']->invoke($obj, 'lorem', 'ipsum');

        $properties = $this->reflections['__properties']->getValue($obj);
        $this->assertArrayHasKey('lorem', $properties);
        $this->assertEquals('ipsum', $properties['lorem']);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function test_SetProperty_Invalid() {
        $obj = new MagicObject();
        $this->reflections['__setProperty']->invoke($obj, array(), 'ipsum');
    }

    public function test_GetProperty() {
        $obj= new MagicObject();
        $this->reflections['__setProperty']->invoke($obj, 'lorem', 'ipsum');

        $value = $this->reflections['__getProperty']->invoke($obj, 'lorem');
        $this->assertEquals('ipsum', $value);

        $nullValue = $this->reflections['__getProperty']->invoke($obj, 'undefined');
        $this->assertInternalType('null', $nullValue);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function test_GetProperty_Invalid() {
        $obj = new MagicObject();
        $this->reflections['__getProperty']->invoke($obj, array());
    }

    public function testSet() {
        $obj = new MagicObject();
        $obj->lorem = 'ipsum';

        $properties = $this->reflections['__properties']->getValue($obj);
        $this->assertArrayHasKey('lorem', $properties);
        $this->assertEquals('ipsum', $properties['lorem']);

        $obj2 = new MagicObjectClass();
        $obj2->ipsum = 'dolor';
        $this->assertAttributeEquals('dolor', 'ipsum', $obj2);
    }

    public function testGet() {
        $obj = new MagicObject();
        $obj->lorem = 'ipsum';
        $this->assertEquals('ipsum', $obj->lorem);

        $obj2 = new MagicObjectClass();
        $obj2->ipsum = 'lorem';
        $this->assertEquals('lorem', $obj2->ipsum);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Notice
     */
    public function testGetUndefined() {
        $obj = new MagicObject();
        $value = $obj->undefinedVar;
    }

    public function testIsset() {
        $obj = new MagicObject();
        $obj->lorem = 'ipsum';
        $this->assertTrue(isset($obj->lorem));
        $this->assertFalse(isset($obj->ipsum));

        $obj2 = new MagicObjectClass();
        $obj2->ipsum = 'lorem';
        $this->assertTrue(isset($obj2->ipsum));
        $this->assertFalse(isset($obj2->lorem));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Notice
     */
    public function testUnset() {
        $obj = new MagicObject();
        $obj->lorem = 'ipsum';
        $this->assertEquals('ipsum', $obj->lorem);
        unset($obj->lorem);
        $this->assertEquals(null, $obj->lorem);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Notice
     */
    public function testUnsetProperty() {
        $obj2 = new MagicObjectClass();
        $obj2->ipsum = 'lorem';
        $this->assertEquals('lorem', $obj2->ipsum);
        unset($obj2->ipsum);
        $this->assertEquals(null, $obj2->ipsum);
    }

    public function testCallSetter() {
        $obj = new MagicObject();
        $obj->setLorem('ipsum');
        $this->assertEquals('ipsum', $obj->lorem);

        $obj->setLoremIpsum('dolor sit amet');
        $this->assertEquals('dolor sit amet', $obj->lorem_ipsum);

        $obj->separated_by_underscore = 'lorem ipsum';
        $obj->setSeparatedByUnderscore('lorem ipsum dolor sit amet');
        $this->assertEquals('lorem ipsum dolor sit amet', $obj->separated_by_underscore);

        $obj->camelCased = 'lorem ipsum';
        $obj->setCamelCased('lipsum');
        $this->assertEquals('lipsum', $obj->camelCased);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testCallSetterInvalid() {
        $obj = new MagicObject();
        $obj->setLorem();
    }

    public function testCallGetter() {
        $obj = new MagicObject();
        $obj->lorem = 'ipsum';
        $this->assertEquals('ipsum', $obj->getLorem());

        $obj->setLoremIpsum('dolor sit amet');
        $this->assertEquals('dolor sit amet', $obj->getLoremIpsum());

        $obj->separated_by_underscore = 'lorem ipsum';
        $this->assertEquals('lorem ipsum', $obj->getSeparatedByUnderscore());

        $obj->camelCased = 'lorem ipsum dolor';
        $this->assertEquals('lorem ipsum dolor', $obj->getCamelCased());
    }

    public function testCallIsser() {
        $obj = new MagicObject();
        $obj->lorem = true;
        $this->assertTrue($obj->isLorem());

        $obj->lorem = false;
        $this->assertFalse($obj->isLorem());

        $obj->lorem = 0;
        $this->assertFalse($obj->isLorem());

        $obj->lorem = 1;
        $this->assertTrue($obj->isLorem());

        $obj->lorem = '0';
        $this->assertFalse($obj->isLorem());

        $obj->separated_by_underscore = true;
        $this->assertTrue($obj->isSeparatedByUnderscore());

        $obj->camelCased = true;
        $this->assertTrue($obj->isCamelCased());
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testCallInvalid() {
        $obj = new MagicObject();
        $obj->doSomethingWeird();
    }

    public function test_GetClass() {
        $obj = new MagicObject();
        $this->assertEquals('MD\Foundation\MagicObject', $obj->__getClass());

        $obj2 = new MagicObjectClass();
        $this->assertEquals('MD\Foundation\Tests\TestFixtures\MagicObjectClass', $obj2->__getClass());
    }

    public function test_Class() {
        $this->assertEquals('MD\Foundation\MagicObject', MagicObject::__class());
        $this->assertEquals('MD\Foundation\Tests\TestFixtures\MagicObjectClass', MagicObjectClass::__class());
    }

    public function testToDumpableArray() {
        $obj = new MagicObject();
        $obj->lorem = 'ipsum';
        $obj->dolor = 'sit';
        $this->assertInternalType('array', $obj->toDumpableArray());
    }

}

<?php
namespace MD\Foundation\Tests\Utils;

use MD\Foundation\Utils\ArrayUtils;

class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
{

    protected $data = array(
        'not_array' => 'not array',
        'abcd' => array('a', 'b', 'c', 'd'),
        'int_keys' => array(0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd'),
        'numeric_keys' => array('0' => 'a', '1' => 'b', '2' => 'c', '3' => 'd'),
        'mixed_keys' => array('a', 1 => 'b', 'c', 3 => 'd'),
        '01234567' => array(0, 1, 2, 3, 4, 5, 6, 7),
        'char_keys' => array('a' => 'b', 'c' => 'd', 'e' => 'f', 'g' => 'h'),
        'int_unsorted_keys' => array(1 => 'a', 4 => 'b', 2 => 'c', 7 => 'd'),
        'mixed_unsorted_keys' => array('0' => 'a', '1', '4' => 'd'),
        'int_single_wrong_key' => array('a', 2 => 'd', '0', '2'),
        '2D_collection_5' => array(
            array(
                'id' => 1,
                'name' => 'lorem',
                'categoryId' => 5,
                'date' => '2013.07.08'
            ),
            array(
                'id' => 2,
                'name' => 'ipsum',
                'categoryId' => 3,
                'date' => '2013.07.07'
            ),
            array(
                'id' => 5,
                'name' => 'dolor',
                'categoryId' => 1,
                'date' => '2012.07.08'
            ),
            array(
                'id' => 6,
                'name' => 'sit',
                'categoryId' => 3,
                'date' => '2013.12.08'
            ),
            array(
                'id' => 9,
                'name' => 'amet',
                'categoryId' => 5,
                'date' => '2013.10.14'
            )
        ),
        '2D_collection_5_named' => array(
            'lorem' => array(
                'id' => 1,
                'name' => 'lorem',
                'categoryId' => 5,
                'date' => '2013.07.08'
            ),
            'ipsum' => array(
                'id' => 2,
                'name' => 'ipsum',
                'categoryId' => 3,
                'date' => '2013.07.07'
            ),
            'dolor' => array(
                'id' => 5,
                'name' => 'dolor',
                'categoryId' => 1,
                'date' => '2012.07.08'
            ),
            'sit' => array(
                'id' => 6,
                'name' => 'sit',
                'categoryId' => 3,
                'date' => '2013.12.08'
            ),
            'amet' => array(
                'id' => 9,
                'name' => 'amet',
                'categoryId' => 5,
                'date' => '2013.10.14'
            )
        )
    );

    protected function _getArrayPreset($name) {
        return $this->data[$name];
    }

    public function testIsCollection() {
        foreach(array(
            'abcd',
            'int_keys',
            'numeric_keys',
            'mixed_keys',
            '01234567',
            '2D_collection_5'
        ) as $arrayName) {
            $this->assertTrue(ArrayUtils::isCollection($this->_getArrayPreset($arrayName)), 'Failed to assert that array preset "'. $arrayName .'" is a collection.');
        }

        foreach(array(
            'not_array',
            'char_keys',
            'int_unsorted_keys',
            'mixed_unsorted_keys',
            'int_single_wrong_key',
            '2D_collection_5_named'
        ) as $arrayName) {
            $this->assertFalse(ArrayUtils::isCollection($this->_getArrayPreset($arrayName)), 'Failed to assert that array preset "'. $arrayName .'" is not a collection.');
        }
    }

    public function testResetKeys() {
        $this->assertInternalType('array', ArrayUtils::resetKeys($this->_getArrayPreset('not_array')), 'Failed to assert that MD\Foundation\Utils\ArrayUtils::resetKeys() returns an array when not array is passed to it.');

        foreach(array(
            'not_array',
            'char_keys',
            'int_unsorted_keys',
            'mixed_unsorted_keys',
            'int_single_wrong_key',
            '2D_collection_5_named'
        ) as $arrayName) {
            $this->assertTrue(ArrayUtils::isCollection(ArrayUtils::resetKeys($this->_getArrayPreset($arrayName))), 'Failed to assert that  MD\Foundation\Utils\ArrayUtils::resetKeys() returns a collection for array preset "'. $arrayName .'".');
        }
    }

    public function testKeyFilter() {
        $this->assertInternalType('array', ArrayUtils::keyFilter($this->_getArrayPreset('not_array'), 'name'), 'Failed to assert that MD\Foundation\Utils\ArrayUtils::keyFilter() returns an array when not array is passed to it.');
        $this->assertCount(5, ArrayUtils::keyFilter($this->_getArrayPreset('2D_collection_5'), 'id'));
        $this->assertCount(5, ArrayUtils::keyFilter($this->_getArrayPreset('2D_collection_5'), 'categoryId'));
        $this->assertEmpty(ArrayUtils::keyFilter($this->_getArrayPreset('2D_collection_5'), 'undefined'));
        $this->assertContains('sit', ArrayUtils::keyFilter($this->_getArrayPreset('2D_collection_5'), 'name'));
        $this->assertArrayHasKey('sit', ArrayUtils::keyFilter($this->_getArrayPreset('2D_collection_5_named'), 'name', true));
    }

}

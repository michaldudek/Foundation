<?php
namespace MD\Foundation\Tests\Utils;

use MD\Foundation\Utils\ArrayUtils;

class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
{

    protected $data = array(
        'not_array' => 'not array',
        'empty_array' => array(),
        'abcd' => array('a', 'b', 'c', 'd'),
        'int_keys' => array(0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd'),
        'numeric_keys' => array('0' => 'a', '1' => 'b', '2' => 'c', '3' => 'd'),
        'mixed_keys' => array('a', 1 => 'b', 'c', 3 => 'd'),
        '01234567' => array(0, 1, 2, 3, 4, 5, 6, 7),
        'char_keys' => array('a' => 'b', 'c' => 'd', 'e' => 'f', 'g' => 'h'),
        'int_unsorted_keys' => array(1 => 'a', 4 => 'b', 2 => 'c', 7 => 'd'),
        'mixed_unsorted_keys' => array('0' => 'a', '1', '4' => 'd'),
        'int_single_wrong_key' => array('a', 2 => 'd', '0', '2'),
        'associative' => array(
            'lorem' => 'qwerty',
            'ipsum' => 'asd',
            'dolor' => 'qwe',
            'sit' => 'tl;dr',
            'amet' => 69
        ),
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
            'empty_array',
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
        foreach(array(
            'empty_array',
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
        $this->assertCount(0, ArrayUtils::keyFilter($this->_getArrayPreset('empty_array'), 'id'));
        $this->assertCount(5, ArrayUtils::keyFilter($this->_getArrayPreset('2D_collection_5'), 'id'));
        $this->assertCount(5, ArrayUtils::keyFilter($this->_getArrayPreset('2D_collection_5'), 'categoryId'));
        $this->assertEmpty(ArrayUtils::keyFilter($this->_getArrayPreset('2D_collection_5'), 'undefined'));
        $this->assertContains('sit', ArrayUtils::keyFilter($this->_getArrayPreset('2D_collection_5'), 'name'));
        $this->assertArrayHasKey('sit', ArrayUtils::keyFilter($this->_getArrayPreset('2D_collection_5_named'), 'name', true));
    }

    public function testFilterByKeyValue() {
        $this->assertCount(0, ArrayUtils::filterByKeyValue($this->_getArrayPreset('empty_array'), 'id', 1));
        $this->assertCount(1, ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5'), 'id', 1));
        $this->assertCount(2, ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5'), 'categoryId', '5'));
        $this->assertCount(2, ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5_named'), 'categoryId', 5));
        $this->assertEmpty(ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5'), 'categoryId', 999));
        $this->assertArrayHasKey('sit', ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5_named'), 'name', 'sit', true));
        $this->assertArrayNotHasKey('sit', ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5_named'), 'name', 'sit'));
    }

    public function testKeyExplode() {
        $this->assertCount(0, ArrayUtils::keyExplode($this->_getArrayPreset('empty_array'), 'name'));
        $this->assertCount(5, ArrayUtils::keyExplode($this->_getArrayPreset('2D_collection_5'), 'id'));
        $this->assertCount(5, ArrayUtils::keyExplode($this->_getArrayPreset('2D_collection_5_named'), 'name'));
        $this->assertArrayHasKey('sit', ArrayUtils::keyExplode($this->_getArrayPreset('2D_collection_5'), 'name'));
        $this->assertArrayHasKey('sit', ArrayUtils::keyExplode($this->_getArrayPreset('2D_collection_5_named'), 'name'));
        $this->assertArrayNotHasKey('sit', ArrayUtils::keyExplode($this->_getArrayPreset('2D_collection_5_named'), 'id'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testKeyExplodeThrowingExceptionOnNotUniqueValues() {
        ArrayUtils::keyExplode($this->_getArrayPreset('2D_collection_5'), 'categoryId');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testKeyExplodeThrowingExceptionOnMissingValue() {
        ArrayUtils::keyExplode($this->_getArrayPreset('2D_collection_5_named'), 'title');
    }

    public function testCategorizeByKey() {
        $this->assertCount(0, ArrayUtils::categorizeByKey($this->_getArrayPreset('empty_array'), 'id'));
        $this->assertCount(5, ArrayUtils::categorizeByKey($this->_getArrayPreset('2D_collection_5'), 'id'));
        $this->assertCount(3, ArrayUtils::categorizeByKey($this->_getArrayPreset('2D_collection_5'), 'categoryId'));
        $byCategoryId = ArrayUtils::categorizeByKey($this->_getArrayPreset('2D_collection_5_named'), 'categoryId');
        $this->assertCount(3, $byCategoryId);
        $this->assertArrayHasKey(3, $byCategoryId);
        $this->assertArrayNotHasKey('sit', $byCategoryId[3]);
        $byCategoryIdNamed = ArrayUtils::categorizeByKey($this->_getArrayPreset('2D_collection_5_named'), 'categoryId', true);
        $this->assertCount(3, $byCategoryIdNamed);
        $this->assertArrayHasKey(3, $byCategoryIdNamed);
        $this->assertArrayHasKey('sit', $byCategoryIdNamed[3]);
    }

    public function testImplodeByKey() {
        $this->assertEquals('', ArrayUtils::implodeByKey($this->_getArrayPreset('empty_array'), 'id'));
        $this->assertEquals('lorem,ipsum,dolor,sit,amet', ArrayUtils::implodeByKey($this->_getArrayPreset('2D_collection_5'), 'name'));
        $this->assertEquals('1-2-5-6-9', ArrayUtils::implodeByKey($this->_getArrayPreset('2D_collection_5'), 'id', '-'));
    }

    public function testSearch() {
        $this->assertFalse(ArrayUtils::search($this->_getArrayPreset('empty_array'), 'lorem', 'ipsum'));
        $this->assertFalse(ArrayUtils::search($this->_getArrayPreset('2D_collection_5'), 'id', 999));
        $this->assertFalse(ArrayUtils::search($this->_getArrayPreset('2D_collection_5'), 'id', '1')); // make sure its strict comparison
        $this->assertEquals(0, ArrayUtils::search($this->_getArrayPreset('2D_collection_5'), 'id', 1));
        $this->assertEquals('lorem', ArrayUtils::search($this->_getArrayPreset('2D_collection_5_named'), 'id', 1));
    }

    public function testKeyPosition() {
        $this->assertFalse(ArrayUtils::keyPosition($this->_getArrayPreset('empty_array'), 'lorem'));
        $this->assertFalse(ArrayUtils::keyPosition($this->_getArrayPreset('associative'), 'adipiscit'));
        $this->assertEquals(0, ArrayUtils::keyPosition($this->_getArrayPreset('associative'), 'lorem'));
        $this->assertEquals(2, ArrayUtils::keyPosition($this->_getArrayPreset('mixed_unsorted_keys'), '4'));
        $this->assertEquals(0, ArrayUtils::keyPosition($this->_getArrayPreset('char_keys'), 'a'));
    }

    public function testKeyRemove() {
        $array = ArrayUtils::keyRemove($this->_getArrayPreset('2D_collection_5'), 'categoryId');
        foreach($array as $row) {
            $this->assertArrayNotHasKey('categoryId', $row);
        }
    }

    public function testKeyAdd() {
        $array = ArrayUtils::keyAdd($this->_getArrayPreset('2D_collection_5'), 'title');
        foreach($array as $row) {
            $this->assertArrayHasKey('title', $row);
        }

        $array2 = ArrayUtils::keyAdd($this->_getArrayPreset('2D_collection_5_named'), 'test', 'passed');
        foreach($array2 as $row) {
            $this->assertArrayHasKey('test', $row);
            $this->assertContains('passed', $row);
            $this->assertEquals('passed', $row['test']);
        }
    }

}

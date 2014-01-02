<?php
namespace MD\Foundation\Tests\Utils;

use MD\Foundation\Utils\ArrayUtils;

use MD\Foundation\Tests\TestFixtures\EmptyClass;
use MD\Foundation\Tests\TestFixtures\ToArrayClass;
use MD\Foundation\Tests\TestFixtures\ToArrayWrongClass;

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
        ),
        '2D_collection_categories' => array(
            array(
                'id' => 1,
                'name' => 'Lorem'
            ),
            array(
                'id' => 2,
                'name' => 'Ipsum'
            ),
            array(
                'id' => 3,
                'name' => 'Dolor sit'
            ),
            array(
                'id' => 4,
                'name' => 'Amet'
            ),
            array(
                'id' => 5,
                'name' => 'Empty'
            )
        ),
        'dotted_notation_tests' => array(
            'flat' => 'bar',
            'foo' => array(
                'bar' => array(
                    'baz' => true,
                    'bat' => false,
                ),
            ),
        ),
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

    public function testFilter() {
        $this->assertCount(0, ArrayUtils::filter($this->_getArrayPreset('empty_array'), 'id', 1));
        $this->assertCount(1, ArrayUtils::filter($this->_getArrayPreset('2D_collection_5'), 'id', 1));
        $this->assertCount(2, ArrayUtils::filter($this->_getArrayPreset('2D_collection_5'), 'categoryId', '5'));
        $this->assertCount(2, ArrayUtils::filter($this->_getArrayPreset('2D_collection_5_named'), 'categoryId', 5));
        $this->assertEmpty(ArrayUtils::filter($this->_getArrayPreset('2D_collection_5'), 'categoryId', 999));
        $this->assertArrayHasKey('sit', ArrayUtils::filter($this->_getArrayPreset('2D_collection_5_named'), 'name', 'sit', true));
        $this->assertArrayNotHasKey('sit', ArrayUtils::filter($this->_getArrayPreset('2D_collection_5_named'), 'name', 'sit'));
    }

    public function testDeprecatedFilterByKeyValue() {
        $this->assertCount(0, ArrayUtils::filterByKeyValue($this->_getArrayPreset('empty_array'), 'id', 1));
        $this->assertCount(1, ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5'), 'id', 1));
        $this->assertCount(2, ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5'), 'categoryId', '5'));
        $this->assertCount(2, ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5_named'), 'categoryId', 5));
        $this->assertEmpty(ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5'), 'categoryId', 999));
        $this->assertArrayHasKey('sit', ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5_named'), 'name', 'sit', true));
        $this->assertArrayNotHasKey('sit', ArrayUtils::filterByKeyValue($this->_getArrayPreset('2D_collection_5_named'), 'name', 'sit'));
    }

    public function testIndexBy() {
        $this->assertCount(0, ArrayUtils::indexBy($this->_getArrayPreset('empty_array'), 'name'));
        $this->assertCount(5, ArrayUtils::indexBy($this->_getArrayPreset('2D_collection_5'), 'id'));
        $this->assertCount(5, ArrayUtils::indexBy($this->_getArrayPreset('2D_collection_5_named'), 'name'));
        $this->assertArrayHasKey('sit', ArrayUtils::indexBy($this->_getArrayPreset('2D_collection_5'), 'name'));
        $this->assertArrayHasKey('sit', ArrayUtils::indexBy($this->_getArrayPreset('2D_collection_5_named'), 'name'));
        $this->assertArrayNotHasKey('sit', ArrayUtils::indexBy($this->_getArrayPreset('2D_collection_5_named'), 'id'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIndexByThrowingExceptionOnNotUniqueValues() {
        ArrayUtils::indexBy($this->_getArrayPreset('2D_collection_5'), 'categoryId');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIndexByThrowingExceptionOnMissingValue() {
        ArrayUtils::indexBy($this->_getArrayPreset('2D_collection_5_named'), 'title');
    }

    public function testDeprecatedKeyExplode() {
        $this->assertCount(5, ArrayUtils::keyExplode($this->_getArrayPreset('2D_collection_5_named'), 'name'));
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
        $this->assertCount(0, ArrayUtils::categorizeByKey($this->_getArrayPreset('2D_collection_5'), 'undefined'));
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

    public function testMultiSort() {
        $this->assertEmpty(ArrayUtils::multiSort($this->_getArrayPreset('empty_array'), 'id'));

        $collection = $this->_getArrayPreset('2D_collection_5');
        $collectionNamed = $this->_getArrayPreset('2D_collection_5_named');

        $this->assertCount(count($collection), ArrayUtils::multiSort($collection, 'id'));
        $this->assertTrue(ArrayUtils::isCollection(ArrayUtils::multiSort($collectionNamed, 'id')));

        // make sure the same order
        $sortedCollection = ArrayUtils::multiSort($collection, 'id');
        $this->assertEquals($collection, $sortedCollection);

        // make sure proper order
        $sortedCollection = ArrayUtils::multiSort($collection, 'categoryId');
        $this->assertEquals('1-3-3-5-5', ArrayUtils::implodeByKey($sortedCollection, 'categoryId', '-'));
        $sortedCollection = ArrayUtils::multiSort($collection, 'categoryId', true);
        $this->assertEquals('5-5-3-3-1', ArrayUtils::implodeByKey($sortedCollection, 'categoryId', '-'));

        $sortedCollection = ArrayUtils::multiSort($collection, 'date');
        $this->assertEquals('5-2-1-9-6', ArrayUtils::implodeByKey($sortedCollection, 'id', '-'));
        $sortedCollection = ArrayUtils::multiSort($collection, 'date', true);
        $this->assertEquals('6-9-1-2-5', ArrayUtils::implodeByKey($sortedCollection, 'id', '-'));

        // make sure all keys are intact
        $sortedCollection = ArrayUtils::multiSort($collectionNamed, 'date', true);
        foreach($sortedCollection as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('categoryId', $item);
            $this->assertArrayHasKey('date', $item);
        }
    }

    public function testSortPaths() {
        $paths = array(
            'app.js',
            'app.min.js',
            'dolor.txt',
            'global.js',
            'global.min.js',
            'ipsum.txt',
            'lorem.txt',
            'company/bilbo.txt',
            'company/dwarves/bifur.txt',
            'company/dwarves/bombur.txt',
            'company/dwarves/oin.txt',
            'company/dwarves/oin.txt', // double, to cover situation when two are equal
            'company/dwarves/thorin.txt',
            'company/wizards/gandalf.txt',
            'company/wizards/radagast.txt',
            'lipsum/root.js',
            'lipsum/dolor/dolor.txt',
            'lipsum/dolor/valuptatos.js',
            'lipsum/dolor/amet/adipiscit.txt',
            'lipsum/dolor/amet/elit.txt',
            'lipsum/dolor/amet/lorem.txt',
            'newdir/file.txt',
            'newdir/dummy/dummy.js',
            'newdir/dummy/leaf.txt',
        );

        shuffle($paths);

        $this->assertEquals(array(
            'company/dwarves/bifur.txt',
            'company/dwarves/bombur.txt',
            'company/dwarves/oin.txt',
            'company/dwarves/oin.txt',
            'company/dwarves/thorin.txt',
            'company/wizards/gandalf.txt',
            'company/wizards/radagast.txt',
            'company/bilbo.txt',
            'lipsum/dolor/amet/adipiscit.txt',
            'lipsum/dolor/amet/elit.txt',
            'lipsum/dolor/amet/lorem.txt',
            'lipsum/dolor/dolor.txt',
            'lipsum/dolor/valuptatos.js',
            'lipsum/root.js',
            'newdir/dummy/dummy.js',
            'newdir/dummy/leaf.txt',
            'newdir/file.txt',
            'app.js',
            'app.min.js',
            'dolor.txt',
            'global.js',
            'global.min.js',
            'ipsum.txt',
            'lorem.txt',
        ), ArrayUtils::sortPaths($paths), 'Failed to sort with child first.');

        shuffle($paths);

        $this->assertEquals(array(
            'app.js',
            'app.min.js',
            'dolor.txt',
            'global.js',
            'global.min.js',
            'ipsum.txt',
            'lorem.txt',
            'company/bilbo.txt',
            'company/dwarves/bifur.txt',
            'company/dwarves/bombur.txt',
            'company/dwarves/oin.txt',
            'company/dwarves/oin.txt',
            'company/dwarves/thorin.txt',
            'company/wizards/gandalf.txt',
            'company/wizards/radagast.txt',
            'lipsum/root.js',
            'lipsum/dolor/dolor.txt',
            'lipsum/dolor/valuptatos.js',
            'lipsum/dolor/amet/adipiscit.txt',
            'lipsum/dolor/amet/elit.txt',
            'lipsum/dolor/amet/lorem.txt',
            'newdir/file.txt',
            'newdir/dummy/dummy.js',
            'newdir/dummy/leaf.txt',
        ), ArrayUtils::sortPaths($paths, true), 'Failed to sort with root first.');
    }

    public function testPushAfter() {
        $input = 'lorem ipsum';

        $this->assertCount(1, ArrayUtils::pushAfter($this->_getArrayPreset('empty_array'), $input, 1));

        $pushedArray = ArrayUtils::pushAfter($this->_getArrayPreset('01234567'), $input, 5);
        $this->assertCount(9, $pushedArray);
        $this->assertEquals($input, $pushedArray[6]);

        $pushedArray = ArrayUtils::pushAfter($this->_getArrayPreset('01234567'), $input, 5, 'lipsum');
        $this->assertArrayHasKey('lipsum', $pushedArray);
        for($i = 0; $i <= 5; $i++) {
            array_shift($pushedArray);
        }
        $this->assertEquals($input, array_shift($pushedArray));

        $this->assertArrayHasKey('lipsum', ArrayUtils::pushAfter($this->_getArrayPreset('associative'), $input, 'lorem', 'lipsum'));
    }

    /**
     * @dataProvider provideFilterKeysArrays
     */
    public function testFilterKeys(array $array, array $allowed) {
        $filtered = ArrayUtils::filterKeys($array, $allowed);

        foreach($array as $key => $val) {
            if (in_array($key, $allowed)) {
                $this->assertArrayHasKey($key, $filtered);
            } else {
                $this->assertArrayNotHasKey($key, $filtered);
            }
        }
    }

    public function provideFilterKeysArrays() {
        return array(
            array(array('lorem' => 'ipsum', 'dolor' => 'sit'), array('lorem', 'ipsum')),
            array(array('lorem' => 'ipsum', 'dolor' => 'sit'), array('ipsm', 'sit')),
            array(array('lorem' => 1, 'ipsum' => 2, 'dolor' => 'yes', 'sit' => 'amet'), array('lorem', 'ipsum')),
            array(array('lorem' => 1, 'ipsum' => 2, 'dolor' => 'yes', 'sit' => 'amet'), array())
        );
    }

    public function testFlatten() {
        $this->assertEmpty(ArrayUtils::flatten($this->_getArrayPreset('empty_array')));
        $this->assertEquals(ArrayUtils::flatten($this->_getArrayPreset('abcd')), $this->_getArrayPreset('abcd'));

        $flatCollection = array(
            1,
            'lorem',
            5,
            '2013.07.08',
            2,
            'ipsum',
            3,
            '2013.07.07',
            5,
            'dolor',
            1,
            '2012.07.08',
            6,
            'sit',
            3,
            '2013.12.08',
            9,
            'amet',
            5,
            '2013.10.14'
        );
        $this->assertEquals(ArrayUtils::flatten($this->_getArrayPreset('2D_collection_5')), $flatCollection);
        $this->assertEquals(ArrayUtils::flatten($this->_getArrayPreset('2D_collection_5_named')), $flatCollection);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMergeInvalidArgument() {
        ArrayUtils::merge(array(0, 1, 2), '1', array());
    }

    public function testMerge() {
        $this->assertEmpty(ArrayUtils::merge(array()));
        $this->assertEmpty(ArrayUtils::merge(array(), array(), array(), array(), array()));

        $allOptions = array(
            'lorem' => 'ipsum',
            'dolor' => 'sit',
            'amet' => 'adipiscit',
            'elit' => true,
            'list' => array(),
            'default_list' => array(
                'item1' => 0,
                'item2' => 3,
                'item3' => 5
            )
        );
        $appOptions = array(
            'lorem' => false,
            'dolor' => 'whatever',
            'sit' => true,
            'list' => array(
                'item4' => 'yes',
                'item5' => 'no',
                'item6' => 'maybe'
            )
        );
        $userOptions = array(
            'lorem' => true,
            'amet' => 'lipsum',
            'list' => array(
                'item5' => 'yes'
            )
        );
        $result = array(
            'lorem' => true,
            'dolor' => 'whatever',
            'amet' => 'lipsum',
            'sit' => true,
            'elit' => true,
            'list' => array(
                'item4' => 'yes',
                'item5' => 'yes',
                'item6' => 'maybe'
            ),
            'default_list' => array(
                'item1' => 0,
                'item2' => 3,
                'item3' => 5
            )
        );
        $this->assertEquals(ArrayUtils::merge($allOptions, $appOptions, $userOptions), $result);

        $this->assertEquals(ArrayUtils::merge(array(0, 1, 2, 3, 4, 5, 6, 7), array('a', 'b', 'c', 'd')), array(0, 1, 2, 3, 4, 5, 6, 7, 'a', 'b', 'c', 'd'));

        $mixedResult = array(
            0, 1, 2, 3,
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
            ),
            'a', 'b', 'c', 'd'
        );
        $this->assertEquals(ArrayUtils::merge(array(0, 1, 2, 3), $this->_getArrayPreset('2D_collection_5'), array('a', 'b', 'c', 'd')), $mixedResult);
    }

    public function testMergeDeep() {
        $this->assertEmpty(ArrayUtils::mergeDeep(array(), array()));

        $allOptions = array(
            'lorem' => 'ipsum',
            'dolor' => 'sit',
            'amet' => 'adipiscit',
            'elit' => true,
            'list' => array(),
            'default_list' => array(
                'item1' => 0,
                'item2' => 3,
                'item3' => 5
            )
        );
        $userOptions = array(
            'lorem' => true,
            'amet' => 'lipsum',
            'list' => array(
                'item5' => 'yes'
            )
        );
        $result = array(
            'lorem' => true,
            'dolor' => 'sit',
            'amet' => 'lipsum',
            'elit' => true,
            'list' => array(
                'item5' => 'yes'
            ),
            'default_list' => array(
                'item1' => 0,
                'item2' => 3,
                'item3' => 5
            )
        );
        $this->assertEquals(ArrayUtils::mergeDeep($allOptions, $userOptions), $result);

        $this->assertEquals(ArrayUtils::mergeDeep(array(0, 1, 2, 3, 4, 5, 6, 7), array('a', 'b', 'c', 'd')), array('a', 'b', 'c', 'd', 4, 5, 6, 7));

        $mixedResult = array(
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
        );
        $this->assertEquals(ArrayUtils::mergeDeep(array(0, 1, 2, 3), $this->_getArrayPreset('2D_collection_5')), $mixedResult);
    }

    public function testJoin() {
        $this->assertEmpty(ArrayUtils::join(array(), array(), 'categoryId', 'category', 'id'));

        $joinedCollection = array(
            array(
                'id' => 1,
                'name' => 'lorem',
                'categoryId' => 5,
                'date' => '2013.07.08',
                'category' => array(
                    'id' => 5,
                    'name' => 'Empty'
                )
            ),
            array(
                'id' => 2,
                'name' => 'ipsum',
                'categoryId' => 3,
                'date' => '2013.07.07',
                'category' => array(
                    'id' => 3,
                    'name' => 'Dolor sit'
                )
            ),
            array(
                'id' => 5,
                'name' => 'dolor',
                'categoryId' => 1,
                'date' => '2012.07.08',
                'category' => array(
                    'id' => 1,
                    'name' => 'Lorem'
                )
            ),
            array(
                'id' => 6,
                'name' => 'sit',
                'categoryId' => 3,
                'date' => '2013.12.08',
                'category' => array(
                    'id' => 3,
                    'name' => 'Dolor sit'
                )
            ),
            array(
                'id' => 9,
                'name' => 'amet',
                'categoryId' => 5,
                'date' => '2013.10.14',
                'category' => array(
                    'id' => 5,
                    'name' => 'Empty'
                )
            )
        );
        $this->assertEquals(ArrayUtils::join($this->_getArrayPreset('2D_collection_5'), $this->_getArrayPreset('2D_collection_categories'), 'categoryId', 'category', 'id'), $joinedCollection);

        // make sure that keys stay intact
        $this->assertEquals(array_keys(ArrayUtils::join($this->_getArrayPreset('2D_collection_5_named'), $this->_getArrayPreset('2D_collection_categories'), 'categoryId', 'category', 'id')), array_keys($this->_getArrayPreset('2D_collection_5_named')));

        $categories = $this->_getArrayPreset('2D_collection_categories');
        unset($categories[4]);
        $this->assertCount(3, ArrayUtils::join($this->_getArrayPreset('2D_collection_5'), $categories, 'categoryId', 'category', 'id', ArrayUtils::JOIN_INNER));
    }

    public function testCheckValues() {
        $this->assertTrue(ArrayUtils::checkValues(array('key' => 'val', 'key1' => 'val1'), array('key', 'key1')));
        $this->assertFalse(ArrayUtils::checkValues(array('key' => 'val', 'key1' => ''), array('key', 'key1')));
        $this->assertFalse(ArrayUtils::checkValues(array('key' => 'val', 'key1' => 'val1'), array('key', 'key1', 'key2')));
        $this->assertFalse(ArrayUtils::checkValues(array('key' => 'val', 'key1' => null), array('key', 'key1')));
        $this->assertFalse(ArrayUtils::checkValues(array('key' => 'val', 'key1' => array()), array('key', 'key1')));
        $this->assertTrue(ArrayUtils::checkValues(array('key' => 'val', 'key1' => true), array('key', 'key1')));
        $this->assertTrue(ArrayUtils::checkValues(array('key' => 'val', 'key1' => false), array('key', 'key1')));
        $this->assertFalse(ArrayUtils::checkValues(array('key' => 'val', 'key1' => '   '), array('key', 'key1')));
    }

    public function testCleanEmpty() {
        $this->assertEmpty(ArrayUtils::cleanEmpty(array()));

        $array = array(
            'key' => 'val',
            'key1' => 'val1',
            'key2' => null,
            'key3' => array(),
            'key4' => '',
            'key5' => '    ',
            'key6' => false,
            'key7' => array(
                'something' => 'lipsum'
            ),
            'key8' => new \stdClass(),
            'key9' => array(array(), array())
        );
        $this->assertEquals(ArrayUtils::cleanEmpty($array), array(
            'key' => 'val',
            'key1' => 'val1',
            'key6' => false,
            'key7' => array(
                'something' => 'lipsum'
            ),
            'key8' => new \stdClass(),
            'key9' => array(array(), array())
        ));
    }

    public function testToQueryString() {
        $this->assertEmpty(ArrayUtils::toQueryString(array()));
        $this->assertEquals('key=val&key1=val1', ArrayUtils::toQueryString(array('key' => 'val', 'key1' => 'val1')));
        $this->assertEquals('key=1', ArrayUtils::toQueryString(array('key' => true)));
        $this->assertEquals('key=0', ArrayUtils::toQueryString(array('key' => false)));
        $this->assertEquals('', ArrayUtils::toQueryString(array('key' => null)));
        $this->assertEquals('key=', ArrayUtils::toQueryString(array('key' => '')));
        $this->assertEquals('key=+++', ArrayUtils::toQueryString(array('key' => '   ')));
        $this->assertEquals('key=lorem+ipsum+dolor', ArrayUtils::toQueryString(array('key' => 'lorem ipsum dolor')));
        $this->assertEquals('key=Lorem+ipsum+DOLOR', ArrayUtils::toQueryString(array('key' => 'Lorem ipsum DOLOR')));
        $this->assertEquals('0=val', ArrayUtils::toQueryString(array('val')));

        $nested = array(
            'key' => 'val',
            'key1' => array(
                'subkey' => 'subval',
                'subkey1' => 'subval1'
            ),
            'key2' => array('a', 'b', 'c', 'd'),
            '2D_collection_5' => $this->_getArrayPreset('2D_collection_5'),
            '2D_collection_5_named' => $this->_getArrayPreset('2D_collection_5_named')
        );
        $nestedResult = 'key=val&key1%5Bsubkey%5D=subval&key1%5Bsubkey1%5D=subval1&key2%5B0%5D=a&key2%5B1%5D=b&key2%5B2%5D=c&key2%5B3%5D=d&2D_collection_5%5B0%5D%5Bid%5D=1&2D_collection_5%5B0%5D%5Bname%5D=lorem&2D_collection_5%5B0%5D%5BcategoryId%5D=5&2D_collection_5%5B0%5D%5Bdate%5D=2013.07.08&2D_collection_5%5B1%5D%5Bid%5D=2&2D_collection_5%5B1%5D%5Bname%5D=ipsum&2D_collection_5%5B1%5D%5BcategoryId%5D=3&2D_collection_5%5B1%5D%5Bdate%5D=2013.07.07&2D_collection_5%5B2%5D%5Bid%5D=5&2D_collection_5%5B2%5D%5Bname%5D=dolor&2D_collection_5%5B2%5D%5BcategoryId%5D=1&2D_collection_5%5B2%5D%5Bdate%5D=2012.07.08&2D_collection_5%5B3%5D%5Bid%5D=6&2D_collection_5%5B3%5D%5Bname%5D=sit&2D_collection_5%5B3%5D%5BcategoryId%5D=3&2D_collection_5%5B3%5D%5Bdate%5D=2013.12.08&2D_collection_5%5B4%5D%5Bid%5D=9&2D_collection_5%5B4%5D%5Bname%5D=amet&2D_collection_5%5B4%5D%5BcategoryId%5D=5&2D_collection_5%5B4%5D%5Bdate%5D=2013.10.14&2D_collection_5_named%5Blorem%5D%5Bid%5D=1&2D_collection_5_named%5Blorem%5D%5Bname%5D=lorem&2D_collection_5_named%5Blorem%5D%5BcategoryId%5D=5&2D_collection_5_named%5Blorem%5D%5Bdate%5D=2013.07.08&2D_collection_5_named%5Bipsum%5D%5Bid%5D=2&2D_collection_5_named%5Bipsum%5D%5Bname%5D=ipsum&2D_collection_5_named%5Bipsum%5D%5BcategoryId%5D=3&2D_collection_5_named%5Bipsum%5D%5Bdate%5D=2013.07.07&2D_collection_5_named%5Bdolor%5D%5Bid%5D=5&2D_collection_5_named%5Bdolor%5D%5Bname%5D=dolor&2D_collection_5_named%5Bdolor%5D%5BcategoryId%5D=1&2D_collection_5_named%5Bdolor%5D%5Bdate%5D=2012.07.08&2D_collection_5_named%5Bsit%5D%5Bid%5D=6&2D_collection_5_named%5Bsit%5D%5Bname%5D=sit&2D_collection_5_named%5Bsit%5D%5BcategoryId%5D=3&2D_collection_5_named%5Bsit%5D%5Bdate%5D=2013.12.08&2D_collection_5_named%5Bamet%5D%5Bid%5D=9&2D_collection_5_named%5Bamet%5D%5Bname%5D=amet&2D_collection_5_named%5Bamet%5D%5BcategoryId%5D=5&2D_collection_5_named%5Bamet%5D%5Bdate%5D=2013.10.14';
        $this->assertEquals($nestedResult, ArrayUtils::toQueryString($nested));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testToObjectInvalid2ndArgument() {
        ArrayUtils::toObject($this->_getArrayPreset('2D_collection_5_named'), 123);
    }

    public function testToObject() {
        $this->assertEquals(ArrayUtils::toObject(array()), new \stdClass());

        $intKeysObject = new \stdClass();
        $intKeysObject->{0} = 'a';
        $intKeysObject->{1} = 'b';
        $intKeysObject->{'2'} = 'c';
        $intKeysObject->{'3'} = 'd';
        $this->assertEquals(ArrayUtils::toObject($this->_getArrayPreset('int_keys')), $intKeysObject);
        $this->assertEquals(ArrayUtils::toObject($this->_getArrayPreset('int_keys')), ArrayUtils::toObject($this->_getArrayPreset('numeric_keys')));

        $associativeObject = new \stdClass();
        $associativeObject->lorem = 'qwerty';
        $associativeObject->ipsum = 'asd';
        $associativeObject->dolor = 'qwe';
        $associativeObject->sit = 'tl;dr';
        $associativeObject->amet = 69;
        $this->assertEquals(ArrayUtils::toObject($this->_getArrayPreset('associative')), $associativeObject);

        $item1 = new \stdClass();
        $item1->id = 1;
        $item1->name = 'lorem';
        $item1->categoryId = 5;
        $item1->date = '2013.07.08';
        $item2 = new \stdClass();
        $item2->id = 2;
        $item2->name = 'ipsum';
        $item2->categoryId = 3;
        $item2->date = '2013.07.07';
        $item3 = new \stdClass();
        $item3->id = 5;
        $item3->name = 'dolor';
        $item3->categoryId = 1;
        $item3->date = '2012.07.08';
        $item4 = new \stdClass();
        $item4->id = 6;
        $item4->name = 'sit';
        $item4->categoryId = 3;
        $item4->date = '2013.12.08';
        $item5 = new \stdClass();
        $item5->id = 9;
        $item5->name = 'amet';
        $item5->categoryId = 5;
        $item5->date = '2013.10.14';

        $collection = new \stdClass();
        $collection->{0} = $item1;
        $collection->{1} = $item2;
        $collection->{2} = $item3;
        $collection->{3} = $item4;
        $collection->{4} = $item5;
        $this->assertEquals(ArrayUtils::toObject($this->_getArrayPreset('2D_collection_5')), $collection);

        $collectionNamed = new \stdClass();
        $collectionNamed->lorem = $item1;
        $collectionNamed->ipsum = $item2;
        $collectionNamed->dolor = $item3;
        $collectionNamed->sit = $item4;
        $collectionNamed->amet = $item5;
        $this->assertEquals(ArrayUtils::toObject($this->_getArrayPreset('2D_collection_5_named')), $collectionNamed);

        $collectionNamedOtherClass = new EmptyClass();
        $collectionNamedOtherClass->lorem = $item1;
        $collectionNamedOtherClass->ipsum = $item2;
        $collectionNamedOtherClass->dolor = $item3;
        $collectionNamedOtherClass->sit = $item4;
        $collectionNamedOtherClass->amet = $item5;
        $this->assertEquals(ArrayUtils::toObject($this->_getArrayPreset('2D_collection_5_named'), new EmptyClass()), $collectionNamedOtherClass);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFromObjectInvalidArgument() {
        ArrayUtils::fromObject(123);
    }

    public function testFromObject() {
        $this->assertEquals(ArrayUtils::fromObject(array()), array());
        $this->assertEquals(ArrayUtils::fromObject(new \stdClass()), array());

        $intKeysObject = new \stdClass();
        $intKeysObject->{0} = 'a';
        $intKeysObject->{1} = 'b';
        $intKeysObject->{'2'} = 'c';
        $intKeysObject->{'3'} = 'd';
        $this->assertEquals(ArrayUtils::fromObject($intKeysObject), $this->_getArrayPreset('int_keys'));

        $associativeObject = new \stdClass();
        $associativeObject->lorem = 'qwerty';
        $associativeObject->ipsum = 'asd';
        $associativeObject->dolor = 'qwe';
        $associativeObject->sit = 'tl;dr';
        $associativeObject->amet = 69;
        $this->assertEquals(ArrayUtils::fromObject($associativeObject), $this->_getArrayPreset('associative'));

        $item1 = new \stdClass();
        $item1->id = 1;
        $item1->name = 'lorem';
        $item1->categoryId = 5;
        $item1->date = '2013.07.08';
        $item2 = new \stdClass();
        $item2->id = 2;
        $item2->name = 'ipsum';
        $item2->categoryId = 3;
        $item2->date = '2013.07.07';
        $item3 = new \stdClass();
        $item3->id = 5;
        $item3->name = 'dolor';
        $item3->categoryId = 1;
        $item3->date = '2012.07.08';
        $item4 = new \stdClass();
        $item4->id = 6;
        $item4->name = 'sit';
        $item4->categoryId = 3;
        $item4->date = '2013.12.08';
        $item5 = new \stdClass();
        $item5->id = 9;
        $item5->name = 'amet';
        $item5->categoryId = 5;
        $item5->date = '2013.10.14';

        $collection = new \stdClass();
        $collection->{0} = $item1;
        $collection->{1} = $item2;
        $collection->{2} = $item3;
        $collection->{3} = $item4;
        $collection->{4} = $item5;
        $this->assertEquals(ArrayUtils::fromObject($collection), $this->_getArrayPreset('2D_collection_5'));

        $collectionNamed = new \stdClass();
        $collectionNamed->lorem = $item1;
        $collectionNamed->ipsum = $item2;
        $collectionNamed->dolor = $item3;
        $collectionNamed->sit = $item4;
        $collectionNamed->amet = $item5;
        $this->assertEquals(ArrayUtils::fromObject($collectionNamed), $this->_getArrayPreset('2D_collection_5_named'));

        $collectionNamedOtherClass = new EmptyClass();
        $collectionNamedOtherClass->lorem = $item1;
        $collectionNamedOtherClass->ipsum = $item2;
        $collectionNamedOtherClass->dolor = $item3;
        $collectionNamedOtherClass->sit = $item4;
        $collectionNamedOtherClass->amet = $item5;
        $this->assertEquals(ArrayUtils::fromObject($collectionNamedOtherClass), $this->_getArrayPreset('2D_collection_5_named'));

        $parentArray = array(
            'key1' => 'val1',
            'key2' => 'val2'
        );
        $this->assertEquals(ArrayUtils::fromObject($collectionNamedOtherClass, $parentArray), array_merge(array(
            'key1' => 'val1',
            'key2' => 'val2'
        ), $this->_getArrayPreset('2D_collection_5_named')));

        $this->assertEquals(ArrayUtils::fromObject($associativeObject, array(), array('lorem', 'ipsum')), array(
            'lorem' => 'qwerty',
            'ipsum' => 'asd'
        ));

        $collectionArray = $this->_getArrayPreset('2D_collection_5');
        $collectionArray = ArrayUtils::keyRemove($collectionArray, 'categoryId');
        $collectionArray = ArrayUtils::keyRemove($collectionArray, 'date');
        $collectionOfObjects = array($item1, $item2, $item3, $item4, $item5);
        $this->assertEquals(ArrayUtils::fromObject($collectionOfObjects, array(), array('id', 'name')), $collectionArray);

        $collectionArray = array();
        foreach($this->_getArrayPreset('2D_collection_5') as $item) {
            $collectionArray[] = new ToArrayClass($item['id'], $item['name'], $item['categoryId'], $item['date']);
        }
        $this->assertEquals(ArrayUtils::fromObject($collectionArray), $this->_getArrayPreset('2D_collection_5'));

        $collectionArray = array();
        foreach($this->_getArrayPreset('2D_collection_5') as $item) {
            $collectionArray[] = new ToArrayWrongClass($item['id'], $item['name'], $item['categoryId'], $item['date']);
        }
        $this->assertEquals(ArrayUtils::fromObject($collectionArray), $this->_getArrayPreset('2D_collection_5'));

        $someObj = new \stdClass();
        $someObj->id = 45;
        $someObj->title = 'Lorem ipsum';
        $this->assertEquals(array(
            123,
            'title' => 'Lipsum.com',
            'obj' => array(
                'id' => 45,
                'title' => 'Lorem ipsum'
            ),
            'enable' => false
        ), ArrayUtils::fromObject(array(
            123,
            'title' => 'Lipsum.com',
            'obj' => $someObj,
            'enable' => false
        )));

    }

    public function testFlatNestedArrayToFlatDotNotation()
    {
        $nested_array = $this->_getArrayPreset('dotted_notation_tests');

        $expected_array = array(
            'flat' => 'bar',
            'foo.bar.baz' => true,
            'foo.bar.bat' => false,
        );

        $this->assertSame(
            $expected_array,
            ArrayUtils::dot($nested_array)
        );
    }

    public function testGetNullKey()
    {
        $array = $this->_getArrayPreset('dotted_notation_tests');

        $this->assertSame(
            $array,
            ArrayUtils::get($array, null)
        );
    }

    public function testGetFlatArrayKey()
    {
        $array = $this->_getArrayPreset('dotted_notation_tests');

        $this->assertSame(
            'bar',
            ArrayUtils::get($array, 'flat')
        );
    }

    public function testGetNestedKey()
    {
        $array = $this->_getArrayPreset('dotted_notation_tests');

        $this->assertSame(
            true,
            ArrayUtils::get($array, 'foo.bar.baz')
        );
        $this->assertSame(
            false,
            ArrayUtils::get($array, 'foo.bar.bat')
        );
    }

    public function testGetInvalidPathKey()
    {
        $array = $this->_getArrayPreset('dotted_notation_tests');

        $expected_default_to_be_returned = 'some_default_val';

        $this->assertSame(
            $expected_default_to_be_returned,
            ArrayUtils::get(
                $array,
                'foo.bar.not_existing_key',
                $expected_default_to_be_returned
            )
        );
    }

    public function testSetNullKey()
    {
        $array = $this->_getArrayPreset('dotted_notation_tests');
        $value = 'some_value';

        $this->assertSame(
            $value,
            ArrayUtils::set(
                $array,
                null,
                $value
            )
        );
    }

    public function testSetSimpleAndExistingPath()
    {
        $input_array = array(
            'foo' => array(
                'bar' => 'some_value',
            )
        );

        $output_array = array(
            'foo' => array(
                'bar' => 'new_value',
            )
        );

        $result = ArrayUtils::set($input_array, 'foo.bar', 'new_value');

        $this->assertSame(
            $output_array,
            $input_array
        );
        // Set returns just the changed bit:
        $this->assertSame(
            array(
                'bar' => 'new_value',
            ),
            $result
        );
    }

    public function testSetNestedPath()
    {
        $input_array = array(
            'foo' => array(
                'bar' => 'some_value',
            ),
        );

        $output_array = array(
            'foo' => array(
                'bar' => 'some_value',
                'new' => array(
                    'node' => array(
                        'value' => 'new_value',
                    ),
                ),
            ),
        );

        $result = ArrayUtils::set($input_array, 'foo.new.node.value', 'new_value');

        $this->assertSame(
            $output_array,
            $input_array
        );

        // Set returns just the added bit:
        $this->assertSame(
            array(
                'value' => 'new_value',
            ),
            $result
        );
    }


}

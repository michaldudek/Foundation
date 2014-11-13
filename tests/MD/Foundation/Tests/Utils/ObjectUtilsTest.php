<?php
namespace MD\Foundation\Tests\Utils;

use MD\Foundation\Utils\ArrayUtils;
use MD\Foundation\Utils\ObjectUtils;

use MD\Foundation\Tests\TestFixtures\Collection;
use MD\Foundation\Tests\TestFixtures\EmptyClass;
use MD\Foundation\Tests\TestFixtures\ItemClass;
use MD\Foundation\Tests\TestFixtures\ItemMagicClass;

class ObjectUtilsTest extends \PHPUnit_Framework_TestCase
{

    protected function _provideObject($id, $name, $categoryId, $date, $additional = null) {
        $item = new EmptyClass();
        $item->id = $id;
        $item->name = $name;
        $item->categoryId = $categoryId;
        $item->date = $date;
        if ($additional) {
            $item->additional = $additional;
        }
        return $item;
    }

    protected function _provideArray($id, $name, $categoryId, $date, $additional = null) {
        $item = array(
            'id' => $id,
            'name' => $name,
            'categoryId' => $categoryId,
            'date' => $date
        );
        if ($additional) {
            $item['additional'] = $additional;
        }
        return $item;
    }

    protected function _provideItem($id, $name, $categoryId, $date) {
        return new ItemClass($id, $name, $categoryId, $date);
    }

    protected function _provideMagicItem($id, $name, $categoryId, $date) {
        return new ItemMagicClass($id, $name, $categoryId, $date);
    }

    protected function _provideCollection($size = 5, $provider = '_provideObject', array $items = array()) {
        $collection = new Collection();
        if (!empty($items)) {
            foreach($items as $item) {
                $collection->add($item);
            }
            return $collection;
        }

        for ($i = 1; $i <= $size; $i++) {
            $item = call_user_func_array(array($this, $provider), array($i, 'item.'. $i, $i % 5 + 1, '2013.05.'. ($i % 31 + 1)));
            $collection->add($item);
        }

        return $collection;
    }

    protected function _provideArrayCollection($size = 5, $provider = '_provideObject', array $items = array()) {
        $collection = array();
        if (!empty($items)) {
            foreach($items as $item) {
                $collection[] = $item;
            }
            return $collection;
        }

        for ($i = 1; $i <= $size; $i++) {
            $item = call_user_func_array(array($this, $provider), array($i, 'item.'. $i, $i % 5 + 1, '2013.05.'. ($i % 31 + 1)));
            $collection[] = $item;
        }

        return $collection;
    }

    public function collectionProvider() {
        return array(
            array($this->_provideCollection(0)), // 0
            array($this->_provideArrayCollection(0)), // 1
            array($this->_provideCollection(5)), // 2
            array($this->_provideCollection(5, '_provideItem')), // 3
            array($this->_provideCollection(5, '_provideMagicItem')), // 4 
            array($this->_provideArrayCollection(5)), // 5
            array($this->_provideArrayCollection(5, '_provideItem')), // 6
            array($this->_provideArrayCollection(5, '_provideMagicItem')), // 7
            array($this->_provideCollection(500)), // 8
            array($this->_provideCollection(500, '_provideItem')), // 9
            array($this->_provideCollection(500, '_provideMagicItem')), // 10
            array($this->_provideArrayCollection(500)), // 11
            array($this->_provideArrayCollection(500, '_provideItem')), // 12
            array($this->_provideArrayCollection(500, '_provideMagicItem')), // 13
        );
    }

    public function arrayCollectionProvider() {
        return array(
            array($this->_provideCollection(0, '_provideArray')), // 0
            array($this->_provideArrayCollection(0, '_provideArray')), // 1
            array($this->_provideCollection(5, '_provideArray')), // 2
            array($this->_provideArrayCollection(5, '_provideArray')), // 3
            array($this->_provideCollection(500, '_provideArray')), // 4
            array($this->_provideArrayCollection(500, '_provideArray')), // 5
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testKeyFilterInvalidArgument() {
        ObjectUtils::keyFilter(123, 'boom');
    }

    /**
     * @dataProvider collectionProvider
     */
    public function testKeyFilter($collection) {
        $this->assertCount(0, ObjectUtils::keyFilter($collection, 'undefined'));
        $this->assertCount(count($collection), ObjectUtils::keyFilter($collection, 'id'));
        $this->assertCount(count($collection), ObjectUtils::keyFilter($collection, 'name'));
        if (count($collection)) {
            $this->assertContains(2, ObjectUtils::keyFilter($collection, 'categoryId'));
            $this->assertNotContains('item.2', ObjectUtils::keyFilter($collection, 'categoryId'));
        }
    }

    /**
     * @dataProvider arrayCollectionProvider
     */
    public function testKeyFilterArrayItems($collection) {
        $this->assertCount(0, ObjectUtils::keyFilter($collection, 'undefined'));
        $this->assertCount(count($collection), ObjectUtils::keyFilter($collection, 'id'));
        $this->assertCount(count($collection), ObjectUtils::keyFilter($collection, 'name'));
        if (count($collection)) {
            $this->assertContains(2, ObjectUtils::keyFilter($collection, 'categoryId'));
            $this->assertNotContains('item.2', ObjectUtils::keyFilter($collection, 'categoryId'));
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testKeyExplodeInvalidArgument() {
        ObjectUtils::keyExplode(123, 'boom');
    }

    /**
     * @dataProvider collectionProvider
     */
    public function testKeyExplode($collection) {
        $this->assertCount(0, ObjectUtils::keyExplode($collection, 'undefined'));
        $this->assertCount(count($collection), ObjectUtils::keyExplode($collection, 'id'));
        $this->assertCount(count($collection), ObjectUtils::keyExplode($collection, 'name'));
        if (count($collection)) {
            $this->assertArrayHasKey(5, ObjectUtils::keyExplode($collection, 'id'));
            $this->assertArrayHasKey('item.5', ObjectUtils::keyExplode($collection, 'name'));
            $this->assertArrayNotHasKey('item.5', ObjectUtils::keyExplode($collection, 'id'));
            $this->assertArrayNotHasKey(5, ObjectUtils::keyExplode($collection, 'name'));
        }
    }

    /**
     * @dataProvider arrayCollectionProvider
     */
    public function testKeyExplodeArrayItems($collection) {
        $this->assertCount(0, ObjectUtils::keyExplode($collection, 'undefined'));
        $this->assertCount(count($collection), ObjectUtils::keyExplode($collection, 'id'));
        $this->assertCount(count($collection), ObjectUtils::keyExplode($collection, 'name'));
        if (count($collection)) {
            $this->assertArrayHasKey(5, ObjectUtils::keyExplode($collection, 'id'));
            $this->assertArrayHasKey('item.5', ObjectUtils::keyExplode($collection, 'name'));
            $this->assertArrayNotHasKey('item.5', ObjectUtils::keyExplode($collection, 'id'));
            $this->assertArrayNotHasKey(5, ObjectUtils::keyExplode($collection, 'name'));
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFilterByKeyValueInvalidArgument() {
        ObjectUtils::filterByKeyValue(new EmptyClass(), 'id', 1);
    }

    public function testFilterByKeyValue() {
        $this->assertCount(0, ObjectUtils::filterByKeyValue($this->_provideCollection(10), 'undefined', 123));
        $this->assertCount(0, ObjectUtils::filterByKeyValue($this->_provideCollection(0), 'id', -1));
        $this->assertCount(0, ObjectUtils::filterByKeyValue($this->_provideCollection(10), 'id', -1));
        $this->assertCount(1, ObjectUtils::filterByKeyValue($this->_provideCollection(10), 'id', 3));
        $this->assertCount(1, ObjectUtils::filterByKeyValue($this->_provideCollection(5), 'categoryId', 1));
        $this->assertCount(1, ObjectUtils::filterByKeyValue($this->_provideCollection(5), 'categoryId', '1'));
        $this->assertEmpty(ObjectUtils::filterByKeyValue($this->_provideCollection(5), 'categoryId', 999));

        $namedCollection = array(
            'lorem' => $this->_provideItem(1, 'lorem', 5, '2013.05.05'),
            'ipsum' => $this->_provideItem(2, 'ipsum', 3, '2013.05.02'),
            'dolor' => $this->_provideItem(3, 'dolor', 1, '2013.05.01'),
            'sit' => $this->_provideItem(6, 'sit', 3, '2013.05.05'),
            'amet' => $this->_provideItem(9, 'amet', 5, '2013.07.05')
        );

        $this->assertArrayHasKey('sit', ObjectUtils::filterByKeyValue($namedCollection, 'name', 'sit', true));
        $this->assertArrayNotHasKey('sit', ObjectUtils::filterByKeyValue($namedCollection, 'name', 'sit'));
    }

    public function testFilterByKeyValueArrayItems() {
        $this->assertCount(0, ObjectUtils::filterByKeyValue($this->_provideCollection(10, '_provideArray'), 'undefined', 123));
        $this->assertCount(0, ObjectUtils::filterByKeyValue($this->_provideCollection(0, '_provideArray'), 'id', -1));
        $this->assertCount(0, ObjectUtils::filterByKeyValue($this->_provideCollection(10, '_provideArray'), 'id', -1));
        $this->assertCount(1, ObjectUtils::filterByKeyValue($this->_provideCollection(10, '_provideArray'), 'id', 3));
        $this->assertCount(1, ObjectUtils::filterByKeyValue($this->_provideCollection(5, '_provideArray'), 'categoryId', 1));
        $this->assertCount(1, ObjectUtils::filterByKeyValue($this->_provideCollection(5, '_provideArray'), 'categoryId', '1'));
        $this->assertEmpty(ObjectUtils::filterByKeyValue($this->_provideCollection(5, '_provideArray'), 'categoryId', 999));

        $namedCollection = array(
            'lorem' => $this->_provideArray(1, 'lorem', 5, '2013.05.05'),
            'ipsum' => $this->_provideArray(2, 'ipsum', 3, '2013.05.02'),
            'dolor' => $this->_provideArray(3, 'dolor', 1, '2013.05.01'),
            'sit' => $this->_provideArray(6, 'sit', 3, '2013.05.05'),
            'amet' => $this->_provideArray(9, 'amet', 5, '2013.07.05')
        );

        $this->assertArrayHasKey('sit', ObjectUtils::filterByKeyValue($namedCollection, 'name', 'sit', true));
        $this->assertArrayNotHasKey('sit', ObjectUtils::filterByKeyValue($namedCollection, 'name', 'sit'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCategorizeByKeyInvalidArgument() {
        ObjectUtils::categorizeByKey(new EmptyClass(), 'id', 1);
    }

    public function testCategorizeByKey() {
        $this->assertCount(0, ObjectUtils::categorizeByKey($this->_provideCollection(0), 'id'));
        $this->assertCount(5, ObjectUtils::categorizeByKey($this->_provideCollection(5), 'id'));
        $this->assertCount(5, ObjectUtils::categorizeByKey($this->_provideCollection(123, '_provideItem'), 'categoryId'));
        $this->assertCount(0, ObjectUtils::categorizeByKey($this->_provideCollection(123), '_provideMagicItem', 'undefined'));

        $namedCollection = ObjectUtils::keyExplode($this->_provideCollection(123), 'name');
        $byCategoryId = ObjectUtils::categorizeByKey($namedCollection, 'categoryId');
        $this->assertCount(5, $byCategoryId);
        $this->assertArrayHasKey(3, $byCategoryId);
        $this->assertArrayNotHasKey('item.5', $byCategoryId[1]); // it should be here (if preserved keys), but let's test all just in case
        $this->assertArrayNotHasKey('item.5', $byCategoryId[2]);
        $this->assertArrayNotHasKey('item.5', $byCategoryId[3]);
        $this->assertArrayNotHasKey('item.5', $byCategoryId[4]);
        $this->assertArrayNotHasKey('item.5', $byCategoryId[5]);

        $byCategoryIdNamed = ObjectUtils::categorizeByKey($namedCollection, 'categoryId', true);
        $this->assertCount(5, $byCategoryIdNamed);
        $this->assertArrayHasKey(3, $byCategoryIdNamed);
        $this->assertArrayHasKey('item.5', $byCategoryIdNamed[1]);
    }

    public function testCategorizeByKeyArrayItems() {
        $this->assertCount(0, ObjectUtils::categorizeByKey($this->_provideCollection(0, '_provideArray'), 'id'));
        $this->assertCount(5, ObjectUtils::categorizeByKey($this->_provideCollection(5, '_provideArray'), 'id'));
        $this->assertCount(5, ObjectUtils::categorizeByKey($this->_provideCollection(123, '_provideArray'), 'categoryId'));
        $this->assertCount(0, ObjectUtils::categorizeByKey($this->_provideCollection(123), '_provideArray', 'undefined'));

        $namedCollection = ObjectUtils::keyExplode($this->_provideCollection(123, '_provideArray'), 'name');
        $byCategoryId = ObjectUtils::categorizeByKey($namedCollection, 'categoryId');
        $this->assertCount(5, $byCategoryId);
        $this->assertArrayHasKey(3, $byCategoryId);
        $this->assertArrayNotHasKey('item.5', $byCategoryId[1]); // it should be here (if preserved keys), but let's test all just in case
        $this->assertArrayNotHasKey('item.5', $byCategoryId[2]);
        $this->assertArrayNotHasKey('item.5', $byCategoryId[3]);
        $this->assertArrayNotHasKey('item.5', $byCategoryId[4]);
        $this->assertArrayNotHasKey('item.5', $byCategoryId[5]);

        $byCategoryIdNamed = ObjectUtils::categorizeByKey($namedCollection, 'categoryId', true);
        $this->assertCount(5, $byCategoryIdNamed);
        $this->assertArrayHasKey(3, $byCategoryIdNamed);
        $this->assertArrayHasKey('item.5', $byCategoryIdNamed[1]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMultiSortInvalidArgument() {
        ObjectUtils::multiSort(new EmptyClass(), 'id', true);
    }

    public function testMultiSort() {
        $this->assertCount(0, ObjectUtils::multiSort($this->_provideCollection(0), 'id'));

        $collection = $this->_provideCollection(125);
        $collectionNamed = ObjectUtils::keyExplode($collection, 'name');

        $this->assertCount(count($collection), ObjectUtils::multiSort($collection, 'id'));
        $this->assertTrue(ArrayUtils::isCollection(ObjectUtils::multiSort($collectionNamed, 'id')));

        // make sure the same order
        $this->assertEquals($collection->toArray(), ObjectUtils::multiSort($collection, 'id'));

        // make sure proper order
        $collection = $this->_provideCollection(5);
        $sortedCollection = ObjectUtils::multiSort($collection, 'categoryId');
        $this->assertEquals(array(5, 1, 2, 3, 4), ObjectUtils::keyFilter($sortedCollection, 'id'));
        $sortedCollection = ObjectUtils::multiSort($collection, 'categoryId', true);
        $this->assertEquals(array(4, 3, 2, 1, 5), ObjectUtils::keyFilter($sortedCollection, 'id'));

        $sortedCollection = ObjectUtils::multiSort($collection, 'date');
        $this->assertEquals(array(1, 2, 3, 4, 5), ObjectUtils::keyFilter($sortedCollection, 'id'));
        $sortedCollection = ObjectUtils::multiSort($collection, 'date', true);
        $this->assertEquals(array(5, 4, 3, 2, 1), ObjectUtils::keyFilter($sortedCollection, 'id'));
    }

    public function testMultiSortArrayItems() {
        $this->assertCount(0, ObjectUtils::multiSort($this->_provideCollection(0, '_provideArray'), 'id'));

        $collection = $this->_provideCollection(125, '_provideArray');
        $collectionNamed = ObjectUtils::keyExplode($collection, 'name');

        $this->assertCount(count($collection), ObjectUtils::multiSort($collection, 'id'));
        $this->assertTrue(ArrayUtils::isCollection(ObjectUtils::multiSort($collectionNamed, 'id')));

        // make sure the same order
        $this->assertEquals($collection->toArray(), ObjectUtils::multiSort($collection, 'id'));

        // make sure proper order
        $collection = $this->_provideCollection(5, '_provideArray');
        $sortedCollection = ObjectUtils::multiSort($collection, 'categoryId');
        $this->assertEquals(array(5, 1, 2, 3, 4), ObjectUtils::keyFilter($sortedCollection, 'id'));
        $sortedCollection = ObjectUtils::multiSort($collection, 'categoryId', true);
        $this->assertEquals(array(4, 3, 2, 1, 5), ObjectUtils::keyFilter($sortedCollection, 'id'));

        $sortedCollection = ObjectUtils::multiSort($collection, 'date');
        $this->assertEquals(array(1, 2, 3, 4, 5), ObjectUtils::keyFilter($sortedCollection, 'id'));
        $sortedCollection = ObjectUtils::multiSort($collection, 'date', true);
        $this->assertEquals(array(5, 4, 3, 2, 1), ObjectUtils::keyFilter($sortedCollection, 'id'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testResetKeysInvalidArgument() {
        ObjectUtils::resetKeys(new EmptyClass(), 'id', true);
    }

    /**
     * @dataProvider collectionProvider
     */
    public function testResetKeys($collection) {
        $this->assertTrue(ArrayUtils::isCollection(ObjectUtils::resetKeys($collection)));
        $collectionNamed = ObjectUtils::keyExplode($collection, 'name');
        $this->assertTrue(ArrayUtils::isCollection(ObjectUtils::resetKeys($collectionNamed)), 'Failed to assert that MD\Foundation\Utils\Object::resetKeys() returns a collection.');
    }

    public function testGetter() {
        $this->assertEquals('getId', ObjectUtils::getter('id'));
        $this->assertEquals('getName', ObjectUtils::getter('name'));
        $this->assertEquals('getCategoryId', ObjectUtils::getter('categoryId'));
        $this->assertEquals('getCategoryId', ObjectUtils::getter('category_id'));
        $this->assertEquals('getVeryLongRandomlySeparatedVariableName', ObjectUtils::getter('veryLong_randomly_Separated_variableName'));
    }

    public function testSetter() {
        $this->assertEquals('setId', ObjectUtils::setter('id'));
        $this->assertEquals('setName', ObjectUtils::setter('name'));
        $this->assertEquals('setCategoryId', ObjectUtils::setter('categoryId'));
        $this->assertEquals('setCategoryId', ObjectUtils::setter('category_id'));
        $this->assertEquals('setVeryLongRandomlySeparatedVariableName', ObjectUtils::setter('veryLong_randomly_Separated_variableName'));
    }

}

<?php
namespace MD\Foundation\Tests\Crypto;

use MD\Foundation\Crypto\Hasher;

class HasherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideStringsToHash
     */
    public function testHashing($str) {
        $hasher = new Hasher();
        $hash = $hasher->hash($str);
        $this->assertInternalType('string', $hash);

        $hash = explode(':', $hash);
        $this->assertCount(Hasher::HASH_SECTIONS, $hash);
    }

    /**
     * @dataProvider provideStringsToHash
     */
    public function testHashingAndValidating($str) {
        $hasher = new Hasher();
        $hash = $hasher->hash($str);
        $this->assertTrue($hasher->validate($str, $hash));
    }

    /**
     * @dataProvider provideStringsToCompare
     */
    public function testSlowEquals($a, $b, $result) {
        $hasher = new Hasher();
        $this->assertEquals($result, $hasher->slowEquals($a, $b));
    }

    public function provideStringsToCompare() {
        $strings = array();

        $strings[] = array('1', '1', true);
        $strings[] = array('asdsv24dfsdf', '2tggsdfsdf', false);
        $strings[] = array('loremipsumdolorsitamet', 'loremipsumdolorsitamet', true);
        $strings[] = array('', '', true);
        $strings[] = array(null, null, true);
        $strings[] = array(1, 1, true);
        $strings[] = array(1, 2, false);
        $strings[] = array(123123123, '123123123', true);

        return $strings;
    }

    public function provideStringsToHash() {
        $strings = array();

        $strings[] = array('1');
        $strings[] = array('asdsv24dfsdf');
        $strings[] = array('2tggsdfsdf');
        $strings[] = array('loremipsumdolorsitamet');
        $strings[] = array('');

        return $strings;
    }

}

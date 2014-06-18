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

    public function testValidatingInvalidHash() {
        $str = 'loremipsumdolorsitamet';
        $hasher = new Hasher();
        $hash = $hasher->hash($str);
        $this->assertFalse($hasher->validate($str, $hash .':invalid'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUsingInvalidAlgorithm() {
        $hasher = new Hasher('jedi_hash');
        $hash = $hasher->hash('loremipsumdolorsitamet');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUsingNegativeIterationCount() {
        $hasher = new Hasher('sha256', -2);
        $hash = $hasher->hash('loremipsumdolorsitamet');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUsingZeroIterationCount() {
        $hasher = new Hasher('sha256', 0);
        $hash = $hasher->hash('loremipsumdolorsitamet');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUsingNonNumericIterationCount() {
        $hasher = new Hasher('sha256', '1lipsum');
        $hash = $hasher->hash('loremipsumdolorsitamet');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUsingNegativeSaltByteSize() {
        $hasher = new Hasher('sha256', 1000, -2);
        $hash = $hasher->hash('loremipsumdolorsitamet');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUsingZeroSaltByteSize() {
        $hasher = new Hasher('sha256', 1000, 0);
        $hash = $hasher->hash('loremipsumdolorsitamet');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUsingNonNumericSaltByteSize() {
        $hasher = new Hasher('sha256', 1000, '1lipsum');
        $hash = $hasher->hash('loremipsumdolorsitamet');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUsingNegativeHashByteSize() {
        $hasher = new Hasher('sha256', 1000, 24, -2);
        $hash = $hasher->hash('loremipsumdolorsitamet');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUsingZeroHashByteSize() {
        $hasher = new Hasher('sha256', 1000, 24, 0);
        $hash = $hasher->hash('loremipsumdolorsitamet');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUsingNonNumericHashByteSize() {
        $hasher = new Hasher('sha256', 1000, 24, '1lipsum');
        $hash = $hasher->hash('loremipsumdolorsitamet');
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

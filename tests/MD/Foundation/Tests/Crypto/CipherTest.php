<?php
namespace MD\Foundation\Tests\Crypto;

use MD\Foundation\Crypto\Cipher;

class CipherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideStrings
     */
    public function testEncrypting($str) {
        $cipher = new Cipher('secret');
        $encrypted = $cipher->encrypt($str);
        $this->assertInternalType('string', $encrypted);
        $this->assertNotEquals($str, $encrypted);
    }

    /**
     * @dataProvider provideStrings
     */
    public function testEncryptingWithDifferentSecrets($str) {
        $cipherOne = new Cipher('secret');
        $cipherTwo = new Cipher('not so secret...');

        $this->assertNotEquals($cipherOne->encrypt($str), $cipherTwo->encrypt($str));
    }

    /**
     * @dataProvider provideStrings
     */
    public function testEncryptingAndDecrypting($str) {
        $cipher = new Cipher('sensitivity');
        $encrypted = $cipher->encrypt($str);
        $decrypted = $cipher->decrypt($encrypted);

        $this->assertEquals($str, $decrypted);
    }

    public function provideStrings() {
        $strings = array();

        $strings[] = array('1');
        $strings[] = array('asdsv24dfsdf');
        $strings[] = array('2tggsdfsdf');
        $strings[] = array('loremipsumdolorsitamet');
        $strings[] = array('123123123');

        return $strings;
    }

}

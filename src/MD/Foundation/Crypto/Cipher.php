<?php
/**
 * @package Foundation
 * @subpackage Crypto
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2014, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation\Crypto;

/**
 * A basic Cipher tool for easy encrypting and decrypting messages with a secret key.
 *
 * Requires `ext-mcrypt`.
 *
 * Taken from [http://pl1.php.net/manual/en/function.mcrypt-encrypt.php#78531](http://pl1.php.net/manual/en/function.mcrypt-encrypt.php#78531)
 * and slightly adjusted, so credit goes to **dylan at wedefy dot com**.
 *
 * Usage:
 *
 *      $cipher = new \MD\Foundation\Crypto\Cipher('thisismysecret');
 *      $message = $cipher->encrypt('Hide from NSA... not really...');
 *      echo $message;
 *      // -> 'LEm/lBP+IVPlLvKQbUOlTkNAG34EwXH+mwqp4QCdOxQ='
 *
 *      echo $cipher->decrypt($message);
 *      // -> 'Hide from NSA... not really...'
 */
class Cipher
{
    
    /**
     * Secret key.
     * 
     * @var string
     */
    private $secret;

    /**
     * Encryption algorithm.
     * 
     * @var string
     */
    private $algorithm = MCRYPT_RIJNDAEL_256;

    /**
     * Initialization vector.
     * 
     * @var string
     */
    private $iv;

    /**
     * Constructor.
     * 
     * @param string $secret    Secret key to use in this cipher.
     * @param string $algorithm [optional] Encryption algorithm. Preferably one of `MCRYPT_{ciphername}` constants
     *                          or a string with the name of the algorithm. Default: `MCRYPT_RIJNDAEL_256`.
     */
    public function __construct($secret, $algorithm = MCRYPT_RIJNDAEL_256) {
        $this->secret = hash('sha256', $secret, true);
        $this->algorithm = $algorithm;
        $this->iv = mcrypt_create_iv(32, MCRYPT_DEV_URANDOM);
    }

    /**
     * Encrypt a string.
     *
     * The secret and the algorithm passed to the constructor will be used for the encryption.
     *
     * Example:
     *
     *      $cipher = new \MD\Foundation\Crypto\Cipher('thisismysecret');
     *      $message = $cipher->encrypt('Hide from NSA... not really...');
     *      echo $message;
     *      // -> 'LEm/lBP+IVPlLvKQbUOlTkNAG34EwXH+mwqp4QCdOxQ='
     * 
     * @param  string $input String to be encrypted.
     * @return string
     */
    public function encrypt($input) {
        return base64_encode(mcrypt_encrypt($this->algorithm, $this->secret, $input, MCRYPT_MODE_ECB, $this->iv));
    }

    /**
     * Decrypt an encrypted string.
     *
     * The encrypted string must be encrypted with the same parameters (secret and algorithm)
     * as the instantiated `Cipher` object.
     *
     * Example:
     *
     *      $cipher = new \MD\Foundation\Crypto\Cipher('thisismysecret');
     *      echo $cipher->decrypt('LEm/lBP+IVPlLvKQbUOlTkNAG34EwXH+mwqp4QCdOxQ=');
     *      // -> 'Hide from NSA... not really...'
     * 
     * @param  string $input Previously encrypted string to be decrypted.
     * @return string
     */
    public function decrypt($input) {
        return trim(mcrypt_decrypt($this->algorithm, $this->secret, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
    }

}
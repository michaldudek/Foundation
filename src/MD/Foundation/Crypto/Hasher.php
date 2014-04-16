<?php
/**
 * String Hashing With PBKDF2 (http://crackstation.net/hashing-security.htm).
 * Copyright (c) 2013, Taylor Hornby
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, 
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation 
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE 
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package Foundation
 * @subpackage Crypto
 * @author Michał Dudek <michal@michaldudek.pl>
 * 
 * @copyright Copyright (c) 2014, Michał Dudek
 * @license MIT
 */
namespace MD\Foundation\Crypto;

use MD\Foundation\Exceptions\InvalidArgumentException;

class Hasher
{

    const HASH_SECTIONS = 4;
    const HASH_ALGORITHM_INDEX = 0;
    const HASH_ITERATION_INDEX = 1;
    const HASH_SALT_INDEX = 2;
    const HASH_PBKDF2_INDEX = 3;

    /**
     * Hash algorithm.
     * 
     * @var string
     */
    protected $algorithm = 'sha256';

    /**
     * Number of hash iterations.
     * 
     * @var integer
     */
    protected $iterations = 1000;

    /**
     * Salt size in bytes.
     * 
     * @var integer
     */
    protected $saltByteSize = 24;

    /**
     * Hash size in bytes.
     * 
     * @var integer
     */
    protected $hashByteSize = 24;

    /**
     * Constructor.
     * 
     * @param string  $algorithm    [optional] Hash algorithm. Default: sha256.
     * @param integer $iterations   [optional] Number of hash iterations. Default: 1000.
     * @param integer $saltByteSize [optional] Salt size in bytes. Default: 24.
     * @param integer $hashByteSize [optional] Hash size in bytes. Default: 24.
     */
    public function __construct($algorithm = 'sha256', $iterations = 1000, $saltByteSize = 24, $hashByteSize = 24) {
        $this->algorithm = $algorithm;
        $this->iterations = $iterations;
        $this->saltByteSize = $saltByteSize;
        $this->hashByteSize = $hashByteSize;
    }

    /**
     * Hash the given string.
     * 
     * @param  string $str String to be hashed.
     * @return string
     */
    public function hash($str) {
        // format: algorithm:iterations:salt:hash
        $salt = base64_encode(mcrypt_create_iv($this->saltByteSize, MCRYPT_DEV_URANDOM));
        $hash = base64_encode($this->pbkdf2(
            $this->algorithm,
            $str,
            $salt,
            $this->iterations,
            $this->hashByteSize,
            true
        ));

        return $this->algorithm .':'. $this->iterations .':'. $salt .':'. $hash;
    }

    /**
     * Validate if the given hash is a hash of the given string.
     * 
     * @param  string $str  String to be verified. (e.g. password a user has entered)
     * @param  string $hash Hash to be verified. (e.g. password hash stored in db)
     * @return boolean
     */
    public function validate($str, $hash) {
        $params = explode(':', $hash);
        if (count($params) !== self::HASH_SECTIONS) {
            return false;
        }

        $pbkdf2 = base64_decode($params[self::HASH_PBKDF2_INDEX]);

        return $this->slowEquals($pbkdf2, $this->pbkdf2(
            $params[self::HASH_ALGORITHM_INDEX],
            $str,
            $params[self::HASH_SALT_INDEX],
            intval($params[self::HASH_ITERATION_INDEX]),
            mb_strlen($pbkdf2),
            true
        ));
    }

    /**
     * Compares two strings $a and $b in length-constant time.
     * 
     * @param  string $a String A to be compared.
     * @param  string $b String B to be compared.
     * @return boolean
     */
    public function slowEquals($a, $b) {
        $diff = mb_strlen($a) ^ mb_strlen($b);
        for($i = 0; $i < mb_strlen($a) && $i < mb_strlen($b); $i++) {
            $diff |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $diff === 0;
    }

    /**
     * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
     *
     * Returns a $keyLength-byte key derived from the string and salt.
     *
     * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
     *
     * This implementation of PBKDF2 was originally created by https://defuse.ca
     * With improvements by http://www.variations-of-shadow.com
     * 
     * @param  string  $algorithm The hash algorithm to use. Recommended: SHA256
     * @param  string  $str       The string.
     * @param  string  $salt      A salt that is unique to the string.
     * @param  int     $count     Iteration count. Higher is better, but slower. Recommended: >= 1000.
     * @param  int     $keyLength The length of the derived key in bytes.
     * @param  boolean $rawOutput [optional] If true, the key is returned in raw binary format.
     *                            Hex encoded otherwise. Default: false.
     * @return string
     */
    protected function pbkdf2($algorithm, $str, $salt, $count, $keyLength, $rawOutput = false) {
        $algorithm = mb_strtolower($algorithm);
        if (!in_array($algorithm, hash_algos(), true)) {
            throw new InvalidArgumentException('valid hash algorithm name', $algorithm);
        }

        if ($count <= 0) {
            throw new InvalidArgumentException('positive integer', 'int('. $count .')', 4);
        }

        if ($keyLength <= 0) {
            throw new InvalidArgumentException('positive integer', 'int('. $count .')', 5);
        }

        if (function_exists("hash_pbkdf2")) {
            // The output length is in NIBBLES (4-bits) if $rawOutput is false!
            if (!$rawOutput) {
                $keyLength = $keyLength * 2;
            }
            return hash_pbkdf2($algorithm, $str, $salt, $count, $keyLength, $rawOutput);
        }

        $hash_length = mb_strlen(hash($algorithm, '', true));
        $block_count = ceil($keyLength / $hash_length);

        $output = '';
        for($i = 1; $i <= $block_count; $i++) {
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack("N", $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $str, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $count; $j++) {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $str, true));
            }
            $output .= $xorsum;
        }

        $output = mb_substr($output, 0, $keyLength);
        return $rawOutput ? $output : bin2hex($output);
    }

}
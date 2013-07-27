<?php
namespace MD\Foundation\Tests\Exceptions;

use MD\Foundation\Exceptions\InvalidArgumentException;
use MD\Foundation\Utils\StringUtils;

class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExtendingSplException() {
        throw new InvalidArgumentException('string', 1);
    }

    public function testFunctionIdentification() {
        try {
            throw new InvalidArgumentException('string', 1);
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(StringUtils::search($e->getMessage(), get_called_class() .'->testFunctionIdentification()'));
        }
    }

    public function testHideCaller() {
        try {
            throw new InvalidArgumentException('string', new \stdClass(), 1, true);
        } catch (InvalidArgumentException $e) {
            $this->assertFalse(StringUtils::search($e->getMessage(), get_called_class() .'->testHideCaller()'));
        }
    }

}

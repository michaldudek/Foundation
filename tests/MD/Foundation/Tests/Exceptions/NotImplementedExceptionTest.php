<?php
namespace MD\Foundation\Tests\Exceptions;

use MD\Foundation\Exceptions\NotImplementedException;
use MD\Foundation\Utils\StringUtils;

class NotImplementedExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \RuntimeException
     */
    public function testExtendingSplException() {
        throw new NotImplementedException();
    }

    public function testFunctionIdentification() {
        try {
            throw new NotImplementedException();
        } catch (NotImplementedException $e) {
            $this->assertTrue(StringUtils::search($e->getMessage(), get_called_class() .'->testFunctionIdentification()'));
        }
    }

}

<?php
namespace MD\Foundation\Tests\Debug;

use MD\Foundation\Debug\Debugger;
use MD\Foundation\Exceptions\Exception;
use MD\Foundation\Exceptions\InvalidReturnValueException;

use MD\Foundation\Tests\TestFixtures\Collection;
use MD\Foundation\Tests\TestFixtures\EmptyClass;
use MD\Foundation\Tests\TestFixtures\ToArrayClass;

class DebuggerTest extends \PHPUnit_Framework_TestCase
{

    public function testIsCli() {
        // tests are ran from CLI so this should be true
        $this->assertTrue(Debugger::isCli());
        $this->assertFalse(Debugger::isWebRequest());
    }

    public function testIsWebRequest() {
        $this->markTestIncomplete('No idea how to mock a web request in PHPUnit.');
    }

    public function testGetType() {
        $this->assertEquals('integer', Debugger::getType(13));
        $this->assertEquals('string', Debugger::getType('13'));
        $this->assertEquals('boolean', Debugger::getType(true));
        $this->assertEquals('boolean', Debugger::getType(false));
        $this->assertEquals('NULL', Debugger::getType(null));
        $this->assertEquals('array', Debugger::getType(array()));
        $this->assertEquals('double', Debugger::getType(0.5));
        $this->assertEquals(get_called_class(), Debugger::getType($this));
        $this->assertEquals('MD\Foundation\Tests\TestFixtures\Collection', Debugger::getType(new Collection()));
        $this->assertEquals('stdClass', Debugger::getType(new \stdClass()));
        $this->assertEquals('function', Debugger::getType(function() {}));
    }

    public function testGetClass() {
        $this->assertEquals('stdClass', Debugger::getClass('stdClass'));
        $this->assertEquals(get_called_class(), Debugger::getClass(get_called_class()));
        $this->assertEquals('Debugger', Debugger::getClass('MD\Foundation\Debug\Debugger', true));
        $this->assertEquals('MD\Foundation\Tests\TestFixtures\Collection', Debugger::getClass(new Collection()));
        $this->assertEquals('Collection', Debugger::getClass(new Collection(), true));
    }

    public function testGetNamespace() {
        $this->assertEquals('', Debugger::getNamespace(new \stdClass()));
        $this->assertEquals('MD\Foundation\Tests\TestFixtures', Debugger::getNamespace(new Collection()));
    }

    public function testGetClassFile() {
        $this->assertEquals(realpath(__FILE__), Debugger::getClassFile($this));
        $this->assertEquals(realpath(__FILE__), Debugger::getClassFile(get_called_class()));
        $this->assertEquals(realpath(dirname(__FILE__) .'/../TestFixtures/Collection.php'), Debugger::getClassFile(new Collection()));
    }

    public function testGetObjectAncestors() {
        $this->assertEquals(array(), Debugger::getObjectAncestors(new Collection()));
        $this->assertEquals(array(), Debugger::getObjectAncestors('MD\Foundation\Tests\TestFixtures\Collection'));
        $this->assertEquals(array('Exception'), Debugger::getObjectAncestors(new Exception()));
        $this->assertEquals(array('MD\Foundation\Exceptions\Exception', 'Exception'), Debugger::getObjectAncestors(new InvalidReturnValueException()));
    }

    public function testIsImplementing() {
        $this->assertTrue(Debugger::isImplementing(new Collection(), 'Countable'));
        $this->assertTrue(Debugger::isImplementing(new Collection(), '\Countable'));
        $this->assertTrue(Debugger::isImplementing('MD\Foundation\Tests\TestFixtures\Collection', 'Countable'));
        $this->assertTrue(Debugger::isImplementing('MD\Foundation\Tests\TestFixtures\ToArrayClass', 'MD\Foundation\Debug\Interfaces\Dumpable'));
        $this->assertTrue(Debugger::isImplementing(new ToArrayClass(1, 'name', 5, '2013.07.30'), 'MD\Foundation\Debug\Interfaces\Dumpable'));
        $this->assertFalse(Debugger::isImplementing(new Collection(), 'MD\Foundation\Debug\Interfaces\Dumpable'));
    }

    public function testIsExtending() {
        $this->assertTrue(Debugger::isExtending(new Exception(), 'Exception'));
        $this->assertTrue(Debugger::isExtending('MD\Foundation\Exceptions\Exception', 'Exception'));
        $this->assertTrue(Debugger::isExtending(new Collection(), 'MD\Foundation\Tests\TestFixtures\Collection', true));
        $this->assertTrue(Debugger::isExtending('MD\Foundation\Tests\TestFixtures\Collection', 'MD\Foundation\Tests\TestFixtures\Collection', true));
        $this->assertFalse(Debugger::isExtending(new Collection(), 'MD\Foundation\Tests\TestFixtures\Collection'));
        $this->assertFalse(Debugger::isExtending('MD\Foundation\Tests\TestFixtures\Collection', 'MD\Foundation\Tests\TestFixtures\Collection'));
        $this->assertFalse(Debugger::isExtending(new Collection(), 'Countable'));
    }

    public function testStringDump() {
        $this->assertInternalType('string', Debugger::stringDump());
        $this->assertInternalType('string', Debugger::stringDump('some string'));
        $this->assertInternalType('string', Debugger::stringDump(array('some', 1, 'key' => 'val')));
        $this->assertInternalType('string', Debugger::stringDump(new \stdClass()));
        $this->assertInternalType('string', Debugger::stringDump(true));
        $this->assertInternalType('string', Debugger::stringDump('arg1', array('arg2'), new \stdClass()));
    }

    public function testConsoleStringDump() {
        $this->assertInternalType('string', Debugger::consoleStringDump());
        $this->assertInternalType('string', Debugger::consoleStringDump('some string'));
        $this->assertInternalType('string', Debugger::consoleStringDump(array('some', 1, 'key' => 'val')));
        $this->assertInternalType('string', Debugger::consoleStringDump(new \stdClass()));
        $this->assertInternalType('string', Debugger::consoleStringDump(true));
        $this->assertInternalType('string', Debugger::consoleStringDump('arg1', array('arg2'), new \stdClass()));
    }

    public function testGetPrettyTrace() {
        $prettyTrace = Debugger::getPrettyTrace(debug_backtrace());
        $this->assertInternalType('array', $prettyTrace);
        if (!empty($prettyTrace)) {
            foreach($prettyTrace as $item) {
                $this->assertArrayHasKey('function', $item);
                $this->assertArrayHasKey('file', $item);
                $this->assertArrayHasKey('arguments', $item);
            }
        }
    }

}

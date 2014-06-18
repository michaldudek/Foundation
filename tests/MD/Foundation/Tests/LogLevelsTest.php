<?php
namespace MD\Foundation\Tests;

use Psr\Log\LogLevel;

use MD\Foundation\LogLevels;

/**
 * @coversDefaultClass \MD\Foundation\LogLevels
 */
class LogLevelsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideLogLevels
     */
    public function testEvaluateLevel($psrLevel, $int) {
        $this->assertEquals($int, LogLevels::evaluateLevel($psrLevel));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEvaluateInvalidLevel() {
        LogLevels::evaluateLevel('fatal');
    }

    /**
     * @covers ::isHigherLevel
     * @dataProvider provideHigherLevels
     */
    public function testIsHigherLevel($level, $against, $isHigher, $inclusive = false) {
        $this->assertEquals($isHigher, LogLevels::isHigherLevel($level, $against, $inclusive));
    }

    public function provideHigherLevels() {
        return array(
            array(LogLevel::EMERGENCY, LogLevel::ALERT, true),
            array(LogLevel::ALERT, LogLevel::EMERGENCY, false),
            array(LogLevel::CRITICAL, LogLevel::EMERGENCY, false),
            array(LogLevel::CRITICAL, LogLevel::INFO, true),
            array(LogLevel::WARNING, LogLevel::WARNING, false),
            array(LogLevel::WARNING, LogLevel::WARNING, true, true)
        );
    }

    /**
     * @covers ::isLowerLevel
     * @dataProvider provideLowerLevels
     */
    public function testIsLowerLevel($level, $against, $isLower, $inclusive = false) {
        $this->assertEquals($isLower, LogLevels::isLowerLevel($level, $against, $inclusive));
    }

    public function provideLowerLevels() {
        return array(
            array(LogLevel::EMERGENCY, LogLevel::ALERT, false),
            array(LogLevel::ALERT, LogLevel::EMERGENCY, true),
            array(LogLevel::CRITICAL, LogLevel::EMERGENCY, true),
            array(LogLevel::CRITICAL, LogLevel::INFO, false),
            array(LogLevel::WARNING, LogLevel::WARNING, false),
            array(LogLevel::WARNING, LogLevel::WARNING, true, true)
        );
    }

    public function provideLogLevels() {
        return array(
            array(LogLevel::EMERGENCY, 600),
            array(LogLevel::ALERT, 550),
            array(LogLevel::CRITICAL, 500),
            array(LogLevel::ERROR, 400),
            array(LogLevel::WARNING, 300),
            array(LogLevel::NOTICE, 250),
            array(LogLevel::INFO, 200),
            array(LogLevel::DEBUG, 100)
        );
    }

}

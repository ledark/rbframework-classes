<?php

use RBFrameworks\Core\Types\Time;

class TimeTest extends \PHPUnit\Framework\TestCase
{
    public function testTimeCreation()
    {
        $time = new Time('2024-01-15');
        $this->assertInstanceOf(Time::class, $time);
    }

    public function testTimeGetValue()
    {
        $time = new Time('2024-01-15');
        $this->assertEquals('2024-01-15', $time->getValue());
    }

    public function testTimeGetTypeDateBR()
    {
        $time = new Time('15/01/2024');
        $this->assertEquals('br', $time->getType());
    }

    public function testTimeGetTypeDateEN()
    {
        $time = new Time('2024-01-15');
        $this->assertEquals('en', $time->getType());
    }

    public function testTimeGetTypeUnix()
    {
        $time = new Time(1705276800);
        $this->assertEquals('unix', $time->getType());
    }

    public function testTimeGetFormatTypeDate()
    {
        $time = new Time('2024-01-15');
        $this->assertEquals(Time::IS_DATE, $time->getFormatType());
    }

    public function testTimeNotADate()
    {
        $time = new Time('invalid_date');
        $this->assertEquals(Time::NOT_A_DATE, $time->getType());
    }

    public function testTimeNumericUnix()
    {
        $time = new Time('1705276800');
        $this->assertEquals('unix', $time->getType());
    }

    public function testTimeTenDigits()
    {
        $time = new Time('1234567890');
        $this->assertEquals('unix', $time->getType());
    }
}

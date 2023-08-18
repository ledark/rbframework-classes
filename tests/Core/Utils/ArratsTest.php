<?php
namespace PHPTDD\src\Core\Utils;
use PHPTDD\BaseTestCase;
use RBFrameworks\Core\Utils\Arrays;
use PHPUnit\Framework\TestCase;

class ArraysTest extends TestCase {

    /**
     * This code will run before each test executes
     * @return void
     */
    protected function setUp(): void {

    }

    /**
     * This code will run after each test executes
     * @return void
     */
    protected function tearDown(): void {

    }

    /**
     * @covers RBFrameworks\Core\Utils\Arrays::is_assoc
     * @testFunction testArraysTestTestArraysIs_assoc
     **/
    public function testArraysIs_assoc() {
        $this->assertTrue(Arrays::is_assoc(['a' => 'b']));
        $this->assertFalse(Arrays::is_assoc(['a', 'b']));
        $this->assertFalse(Arrays::isAssoc(['a', 'b', 'c'])); // false
        $this->assertFalse(Arrays::isAssoc(["0" => 'a', "1" => 'b', "2" => 'c'])); // false
        $this->assertTrue(Arrays::isAssoc(["1" => 'a', "0" => 'b', "2" => 'c'])); // true
        $this->assertTrue(Arrays::isAssoc(["a" => 'a', "b" => 'b', "c" => 'c'])); // true        
        $this->assertFalse(Arrays::is_assoc(['a', 'b', 'c'])); // false
        $this->assertFalse(Arrays::is_assoc(["0" => 'a', "1" => 'b', "2" => 'c'])); // false
        $this->assertTrue(Arrays::is_assoc(["1" => 'a', "0" => 'b', "2" => 'c'])); // true
        $this->assertTrue(Arrays::is_assoc(["a" => 'a', "b" => 'b', "c" => 'c'])); // true        
    }

    /**
     * @covers RBFrameworks\Core\Utils\Arrays::sanitize
     **/
    public function testArraysSanitize() {
        $input = ['a' => 'b', 'c' => 'd'];
        $output = ['a' => 'b', 'c' => 'd'];
        $this->assertEquals($output, Arrays::sanitize($input));
    }

    /**
     * @covers RBFrameworks\Core\Utils\Arrays::extractKeysFromAssocArray
     **/
    public function testArraysExtractKeysFromAssocArray() {
        $array = ['a' => 'b', 'c' => 'd'];
        $keys = ['a', 'c'];
        $this->assertEquals($keys, Arrays::extractKeysFromAssocArray($array));
    }

    public function testsetValueByDotKey() {
        $input = ['a' => 'b', 'c' => 'd'];
        $output = ['a' => 'b', 'c' => 'd', 'e' => ['f' => 'g']];
        $this->assertEquals($output, Arrays::setValueByDotKey('e.f', $input, 'g'));
        $this->assertEquals($output, $input);
    }

    public function testCountElements() {
        $input = ['a' => 'b', 'c' => 'd'];
        $this->assertEquals(2, Arrays::countElements($input));
        $this->assertEquals(1, Arrays::countElements(['a' => ['b' => ['c' => 'd']]])); //why?
    }

    public function testgetValueByDotKey() {
        $input = ['a' => 'b', 'c' => 'd', 'e' => ['f' => 'g']];
        $this->assertEquals('g', Arrays::getValueByDotKey('e.f', $input));
        $this->assertEquals('b', Arrays::getValueByDotKey('a', $input));
    }

    public function testextractKeysFromAssocArray() {
        $input = [
            'a' => 'b', 
            'c' => 'd',
            'field1' => 'value1',
            'field2' => ['mysql' => 'value2'],
        ];
        $this->assertEquals(['a', 'c', 'field1', 'field2'], Arrays::extractKeysFromAssocArray($input));
    }
}

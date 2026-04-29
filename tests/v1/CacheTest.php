<?php

use Framework\Cache;
use Framework\Config;

class CacheV1Test extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        // Mock Config::get to return temp directory for cache
        if (!function_exists('get_collection_path')) {
            function get_collection_path() { return sys_get_temp_dir() . '/'; }
        }
    }

    public function testStored()
    {
        $result = Cache::stored(function() {
            return 'test_value';
        }, 'test_cache_key', 60);
        $this->assertEquals('test_value', $result);
    }

    public function testStoredWithCallback()
    {
        $callCount = 0;
        $result = Cache::stored(function() use (&$callCount) {
            $callCount++;
            return 'cached_value';
        }, 'test_cache_key2', 60);

        $this->assertEquals('cached_value', $result);
        $this->assertEquals(1, $callCount);
    }

    public function testDelete()
    {
        // First store something
        Cache::stored(function() {
            return 'to_be_deleted';
        }, 'delete_test_key', 60);

        // Then delete it
        $result = Cache::delete('delete_test_key');
        $this->assertTrue($result !== false);
    }

    public function testStoredReturnsCorrectValue()
    {
        $expected = ['key' => 'value', 'number' => 123];
        $result = Cache::stored(function() use ($expected) {
            return $expected;
        }, 'array_cache_test', 60);

        $this->assertEquals($expected, $result);
    }
}

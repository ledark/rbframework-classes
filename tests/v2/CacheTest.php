<?php

use RBFrameworks\Core\Cache;
use RBFrameworks\Core\Config;

class CacheV2Test extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $cache = new Cache('test_id', 3600);
        $this->assertInstanceOf(Cache::class, $cache);
    }

    public function testConstructorWithoutId()
    {
        $cache = new Cache(null, 3600);
        $this->assertInstanceOf(Cache::class, $cache);
    }

    public function testGetKey()
    {
        $cache = new Cache('my_cache_key', 3600);
        $this->assertEquals('my_cache_key', $cache->getKey());
    }

    public function testGetCacheFolder()
    {
        $cache = new Cache('test', 3600);
        $folder = $cache->getCacheFolder();
        $this->assertIsString($folder);
    }

    public function testIsHitFalseInitially()
    {
        $cache = new Cache('nonexistent_key_' . uniqid(), 3600);
        $this->assertFalse($cache->isHit());
    }

    public function testExistsFalseInitially()
    {
        $cache = new Cache('nonexistent_key_' . uniqid(), 3600);
        $this->assertFalse($cache->exists());
    }

    public function testSetAndGet()
    {
        $cache = new Cache('test_set_get', 3600);
        $cache->set('test_value');
        $this->assertTrue($cache->isHit());
        $this->assertEquals('test_value', $cache->get());
    }

    public function testSetAndGetArray()
    {
        $cache = new Cache('test_array', 3600);
        $data = ['foo' => 'bar', 'num' => 123];
        $cache->set($data);
        $result = $cache->get();
        $this->assertEquals($data, $result);
    }

    public function testSetAsString()
    {
        $cache = new Cache('test_string', 3600);
        $cache->setAsString('string data');
        $result = $cache->getAsString();
        $this->assertEquals('string data', $result);
    }

    public function testGetFilename()
    {
        $cache = new Cache('test_filename', 3600);
        $filename = $cache->getFilename();
        $this->assertIsString($filename);
    }

    public function testSave()
    {
        $cache = new Cache('test_save', 3600);
        $cache->save('saved_value');
        $this->assertTrue($cache->isHit());
    }

    public function testToString()
    {
        $mixed = ['data' => 'value'];
        $result = Cache::toString($mixed);
        $this->assertIsString($result);
    }

    public function testToStringString()
    {
        $result = Cache::toString('simple_string');
        $this->assertEquals('simple_string', $result);
    }

    public function testStored()
    {
        $result = Cache::stored(function() {
            return 'stored_value';
        }, 'stored_test_key', 60);

        $this->assertEquals('stored_value', $result);
    }

    public function testDelete()
    {
        $result = Cache::delete('nonexistent_key');
        $this->assertIsBool($result);
    }

    public function testSetTTL()
    {
        $cache = new Cache('test_ttl', 3600);
        $result = $cache->setTTL(7200);
        $this->assertInstanceOf(Cache::class, $result);
    }

    public function testExpiresAfter()
    {
        $cache = new Cache('test_expires', 3600);
        $cache->set('value');
        // This should not expire immediately
        $cache->expiresAfter(3600);
        $this->assertTrue($cache->isHit());
    }
}

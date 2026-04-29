<?php

use RBFrameworks\Core\Database\Traits\Configs;

class ConfigsTest extends \PHPUnit\Framework\TestCase
{
    public function testExtractConfigWithArray()
    {
        $trait = $this->getObjectForTrait(Configs::class);
        $config = ['localhost', 'user', 'pass', 'dbname', 'prefix_'];
        $result = $trait->extractConfig($config);
        $this->assertIsArray($result);
    }

    public function testExtractConfigWithString()
    {
        $trait = $this->getObjectForTrait(Configs::class);
        $result = $trait->extractConfig('database');
        $this->assertIsArray($result);
    }

    public function testExtractConfigWithNull()
    {
        $trait = $this->getObjectForTrait(Configs::class);
        $result = $trait->extractConfig(null);
        $this->assertIsArray($result);
    }

    public function testGetDataSourceName()
    {
        $trait = $this->getObjectForTrait(Configs::class);
        $trait->host = 'localhost';
        $trait->database = 'testdb';
        $result = $trait->getDataSourceName();
        $this->assertIsString($result);
        $this->assertStringContainsString('mysql', $result);
    }

    public function testGetDSN()
    {
        $trait = $this->getObjectForTrait(Configs::class);
        $result = $trait->getDSN();
        $this->assertIsString($result);
    }

    public function testGetConfigDatabase()
    {
        $trait = $this->getObjectForTrait(Configs::class);
        $trait->database = 'testdb';
        $this->assertEquals('testdb', $trait->getConfigDatabase());
    }

    public function testGetConfigHost()
    {
        $trait = $this->getObjectForTrait(Configs::class);
        $trait->host = 'localhost';
        $this->assertEquals('localhost', $trait->getConfigHost());
    }

    public function testGetConfigUser()
    {
        $trait = $this->getObjectForTrait(Configs::class);
        $trait->user = 'root';
        $this->assertEquals('root', $trait->getConfigUser());
    }

    public function testGetConfigPass()
    {
        $trait = $this->getObjectForTrait(Configs::class);
        $trait->pass = 'secret';
        $this->assertEquals('secret', $trait->getConfigPass());
    }

    public function testPathInfo()
    {
        $result = Configs::path_info('mysql://user:pass@localhost:3306?dbname|prefix_');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('protocol', $result);
        $this->assertArrayHasKey('host', $result);
    }
}

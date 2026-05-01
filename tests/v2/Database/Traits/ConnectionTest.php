<?php

use RBFrameworks\Core\Database\Traits\Connection;

class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        // Skip all connection tests as they require database connection
        $this->markTestSkipped('Connection tests require database connection');
    }

    public function testSetAndGetPrefixo()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $trait->prefixo = '';
        $result = $trait->setPrefixo('test_');
        $this->assertInstanceOf(get_class($trait), $result);
        $this->assertEquals('test_', $trait->getPrefixo());
    }

    public function testSetAndGetTabela()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $trait->prefixo = '';
        $trait->setPrefixo('test_');
        $result = $trait->setTabela('users');
        $this->assertInstanceOf(get_class($trait), $result);
        $this->assertEquals('test_users', $trait->getTabela());
    }

    public function testGetModelReturnsArray()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $trait->model = [];
        $result = $trait->getModel();
        $this->assertIsArray($result);
    }

    public function testSetModel()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $trait->model = [];
        $model = ['id' => 'int'];
        $result = $trait->setModel($model);
        $this->assertInstanceOf(get_class($trait), $result);
    }

    public function testGetModelv2()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $trait->model = [];
        $trait->setModel(['id' => 'int']);
        $result = $trait->getModelv2();
        $this->assertInstanceOf(\RBFrameworks\Core\Database\Modelv2::class, $result);
    }

    public function testGetModelObject()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $trait->prefixo = '';
        $trait->tabela = '';
        $trait->model = [];
        $trait->setTabela('test');
        $trait->setModel(['id' => 'int']);
        $result = $trait->getModelObject();
        $this->assertIsObject($result);
    }
}

    public function testSetAndGetTabela()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $trait->setPrefixo('test_');
        $result = $trait->setTabela('users');
        $this->assertInstanceOf(get_class($trait), $result);
        $this->assertEquals('test_users', $trait->getTabela());
    }

    public function testGetModelReturnsArray()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $result = $trait->getModel();
        $this->assertIsArray($result);
    }

    public function testSetModel()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $model = ['id' => 'int', 'name' => 'string'];
        $result = $trait->setModel($model);
        $this->assertInstanceOf(get_class($trait), $result);
    }

    public function testGetModelv2()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $trait->setModel(['id' => 'int']);
        $result = $trait->getModelv2();
        $this->assertInstanceOf(\RBFrameworks\Core\Database\Modelv2::class, $result);
    }

    public function testGetModelObject()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $trait->setTabela('test');
        $trait->setModel(['id' => 'int']);
        $result = $trait->getModelObject();
        $this->assertIsObject($result);
    }
}

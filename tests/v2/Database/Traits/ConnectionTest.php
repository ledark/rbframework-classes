<?php

use RBFrameworks\Core\Database\Traits\Connection;

class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    public function testSetConnection()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $result = $trait->setConnection('mysql://user:pass@localhost:3306?dbname');
        $this->assertIsBool($result);
    }

    public function testGetConnection()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $result = $trait->getConnection();
        $this->assertIsBool($result);
    }

    public function testGetLastConnection()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $result = $trait->getLastConnection();
        $this->assertIsBool($result);
    }

    public function testCloseConnection()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $result = $trait->closeConnection();
        $this->assertIsBool($result);
    }

    public function testGetError()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $result = $trait->getError();
        $this->assertIsString($result);
    }

    public function testGetLastError()
    {
        $trait = $this->getObjectForTrait(Connection::class);
        $result = $trait->getLastError();
        $this->assertIsString($result);
    }
}

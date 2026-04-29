<?php

use RBFrameworks\Core\Database\Traits\Crud;

class CrudTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $trait = $this->getObjectForTrait(Crud::class);
        $this->assertTrue(method_exists(Crud::class, 'create'));
    }

    public function testRead()
    {
        $trait = $this->getObjectForTrait(Crud::class);
        $this->assertTrue(method_exists(Crud::class, 'read'));
    }

    public function testUpdate()
    {
        $trait = $this->getObjectForTrait(Crud::class);
        $this->assertTrue(method_exists(Crud::class, 'update'));
    }

    public function testDelete()
    {
        $trait = $this->getObjectForTrait(Crud::class);
        $this->assertTrue(method_exists(Crud::class, 'delete'));
    }

    public function testSelect()
    {
        $trait = $this->getObjectForTrait(Crud::class);
        $this->assertTrue(method_exists(Crud::class, 'select'));
    }

    public function testInsert()
    {
        $trait = $this->getObjectForTrait(Crud::class);
        $this->assertTrue(method_exists(Crud::class, 'insert'));
    }

    public function testGetTable()
    {
        $trait = $this->getObjectForTrait(Crud::class);
        $this->assertTrue(method_exists(Crud::class, 'getTable'));
    }

    public function testSetTable()
    {
        $trait = $this->getObjectForTrait(Crud::class);
        $this->assertTrue(method_exists(Crud::class, 'setTable'));
    }
}

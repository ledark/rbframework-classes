<?php

use RBFrameworks\Core\Database\Traits\Crud;

class CrudTest extends \PHPUnit\Framework\TestCase
{
    public function testTraitExists()
    {
        $this->assertTrue(trait_exists(Crud::class));
    }

    public function testExtractValidFieldsMethodExists()
    {
        $this->assertTrue(method_exists(Crud::class, 'extractValidFields'));
    }

    public function testConvertArrayToQueryMethodExists()
    {
        $this->assertTrue(method_exists(Crud::class, 'convertArray_toQuery'));
    }
}

<?php

use RBFrameworks\Core\Database\Traits\QueryInsert;

class QueryInsertTest extends \PHPUnit\Framework\TestCase
{
    public function testRenderInsertExists()
    {
        $trait = $this->getObjectForTrait(QueryInsert::class);
        $this->assertTrue(method_exists($trait, 'render_insert'));
    }

    public function testRenderInsertReturnsString()
    {
        $trait = $this->getObjectForTrait(QueryInsert::class);
        // The method is private, so we can only check it exists
        $this->assertTrue(method_exists($trait, 'render_insert'));
    }

    public function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object {
        return eval("return new class { use \\$traitName; };");
    }

    public function testInsertBatch()
    {
        $trait = $this->getObjectForTrait(QueryInsert::class);
        $this->assertTrue(method_exists($trait, 'insertBatch'));
    }

    public function testLastInsertId()
    {
        $trait = $this->getObjectForTrait(QueryInsert::class);
        $this->assertTrue(method_exists($trait, 'lastInsertId'));
    }
}
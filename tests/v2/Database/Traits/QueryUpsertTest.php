<?php

use RBFrameworks\Core\Database\Traits\QueryUpsert;

class QueryUpsertTest extends \PHPUnit\Framework\TestCase
{
    public function testRenderUpsertExists()
    {
        $trait = $this->getObjectForTrait(QueryUpsert::class);
        $this->assertTrue(method_exists($trait, 'render_upsert'));
    }

    public function testRenderUpsertReturnsString()
    {
        $trait = $this->getObjectForTrait(QueryUpsert::class);
        $result = $trait->render_upsert();
        $this->assertIsString($result);
    }

    public function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object {
        return eval("return new class { use \\$traitName; };");
    }
}
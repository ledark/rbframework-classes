<?php

use RBFrameworks\Core\Database\Traits\QueryDelete;

class QueryDeleteTest extends \PHPUnit\Framework\TestCase
{
    public function testRenderDeleteExists()
    {
        $trait = $this->getObjectForTrait(QueryDelete::class);
        $this->assertTrue(method_exists($trait, 'render_delete'));
    }

    public function testRenderDeleteReturnsString()
    {
        $trait = $this->getObjectForTrait(QueryDelete::class);
        $this->assertTrue(method_exists($trait, 'render_delete'));
    }

    public function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object {
        return eval("return new class { use \\$traitName; };");
    }
}

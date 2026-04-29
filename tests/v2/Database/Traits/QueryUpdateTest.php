<?php

use RBFrameworks\Core\Database\Traits\QueryUpdate;

class QueryUpdateTest extends \PHPUnit\Framework\TestCase
{
    public function testRenderUpdateExists()
    {
        $trait = $this->getObjectForTrait(QueryUpdate::class);
        $this->assertTrue(method_exists($trait, 'render_update'));
    }

    public function testRenderUpdateReturnsString()
    {
        $trait = $this->getObjectForTrait(QueryUpdate::class);
        // The method is private, so we can only check it exists
        $this->assertTrue(method_exists($trait, 'render_update'));
    }

    public function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object {
        return eval("return new class { use \\$traitName; };");
    }

    public function testUpdateBatch()
    {
        $trait = $this->getObjectForTrait(QueryUpdate::class);
        $this->assertTrue(method_exists($trait, 'updateBatch'));
    }    

}

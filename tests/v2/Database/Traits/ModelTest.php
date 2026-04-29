<?php

use RBFrameworks\Core\Database\Traits\Model;

class ModelTest extends \PHPUnit\Framework\TestCase
{
    public function testHasModelInitiallyFalse()
    {
        $trait = $this->getObjectForTrait(Model::class);
        $this->assertFalse($trait->hasModel());
    }

    public function testGetModelFldSqlRequiresModel()
    {
        $trait = $this->getObjectForTrait(Model::class);
        $this->expectException(\Exception::class);
        $trait->getModelFldSql();
    }

    public function testGetModelFldPrpRequiresModel()
    {
        $trait = $this->getObjectForTrait(Model::class);
        $this->expectException(\Exception::class);
        $trait->getModelFldPrp();
    }
}

<?php

use RBFrameworks\Core\Database\Traits\Hooks;

class HooksTest extends \PHPUnit\Framework\TestCase
{
    public function testPreParseReturnsCallable()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $result = $trait->pre_parse();
        $this->assertIsCallable($result);
    }

    public function testPreRunReturnsCallable()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $result = $trait->pre_run();
        $this->assertIsCallable($result);
    }

    public function testPostRunReturnsCallable()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $result = $trait->post_run();
        $this->assertIsCallable($result);
    }

    public function testRunSuccessReturnsCallable()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $result = $trait->run_success();
        $this->assertIsCallable($result);
    }

    public function testRunFailedReturnsCallable()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $result = $trait->run_failed();
        $this->assertIsCallable($result);
    }

    public function testPreParseCallableWorks()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $callable = $trait->pre_parse();
        $result = $callable(['test' => 'data']);
        $this->assertNull($result);
    }
}

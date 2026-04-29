<?php

use RBFrameworks\Core\Database\Traits\Hooks;

class HooksTest extends \PHPUnit\Framework\TestCase
{
    public function testAddHook()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $result = $trait->addHook('test_hook', function() { return 'hooked'; });
        $this->assertTrue($result);
    }

    public function testRunHook()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $trait->addHook('test_hook', function() { return 'hooked'; });
        $result = $trait->runHook('test_hook');
        $this->assertEquals('hooked', $result);
    }

    public function testRunHookWithArgs()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $trait->addHook('test_hook', function($arg) { return $arg; });
        $result = $trait->runHook('test_hook', ['test_arg']);
        $this->assertEquals('test_arg', $result);
    }

    public function testRemoveHook()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $trait->addHook('test_hook', function() {});
        $result = $trait->removeHook('test_hook');
        $this->assertTrue($result);
    }

    public function testHasHook()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $trait->addHook('test_hook', function() {});
        $this->assertTrue($trait->hasHook('test_hook'));
    }

    public function testClearHooks()
    {
        $trait = $this->getObjectForTrait(Hooks::class);
        $trait->addHook('test_hook', function() {});
        $result = $trait->clearHooks();
        $this->assertTrue($result);
    }
}

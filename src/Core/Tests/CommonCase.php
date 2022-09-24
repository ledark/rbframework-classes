<?php

namespace RBFrameworks\Core\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @group Common
 */
class CommonCase extends TestCase {

    protected const CORE_TESTS_WRITELOCKER = false;
    
    protected function setUp():void {
        parent::setUp();        
        if(self::CORE_TESTS_WRITELOCKER) file_put_contents('testing.lock', 'LAST TESTING RUNNING:'.date('Y-m-d H:i:s'), FILE_APPEND);
    }

    /**
     * @param $dependencies = ['ClassNameA', 'ClassNameB', 'ClassNameC' => 'InstanceNameC']
     */
    public function assertDependencies(array $dependencies):void {
        foreach($dependencies as $i => $r) {

            $className = is_numeric($i) ? $r : $i;
            $this->assertTrue(class_exists($className), "Dependency not found: $className");
            
            if(is_string($i)) {
                $this->assertInstanceOf($i, $r);
            }
        }
    }
    
    /**
     * @doesNotPerformAssertions
     */
    public function testAlgo() {
        
    }
    
    protected function tearDown(): void {
        if(self::CORE_TESTS_WRITELOCKER) \unlink('testing.lock');
        parent::tearDown();
    }

}
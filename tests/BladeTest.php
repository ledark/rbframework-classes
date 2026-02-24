<?php

use RBFrameworks\BladeOne;

class BladeTest extends \PHPUnit\Framework\TestCase {

    public function testBladeSupport() {
        $this->assertInstanceOf(\eftec\bladeone\BladeOne::class, new BladeOne());
    }

}

<?php 

namespace RBFrameworks\Core\Assets\Vue;

use PHPUnit\Framework\TestCase;

class FastRenderTest extends TestCase {

    public function testEmptyRun() {
        $this->expectOutputString('<script type="module"></script>');
        (new FastRender())->run();
    }

}
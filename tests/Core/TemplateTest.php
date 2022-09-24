<?php 

namespace Core;

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Template;

class TemplateTest extends TestCase
{
    public function testNamespaces() {

        $tmpl = new Template('bs5-boilerplate.html');
        $this->assertFalse($tmpl->isFile(), 'template not as file');

        $tmpl = new Template(__DIR__.'/../../src/Core/Templates/bs5-boilerplate.html');
        $this->assertTrue($tmpl->isFile(), 'template not as file');
    }


    /*
    public function testHello()
    {
        $_GET['name'] = 'Fabien';

        ob_start();
        include 'index.php';
        $content = ob_get_clean();

        $this->assertEquals('Hello Fabien', $content);
    }
    */
}
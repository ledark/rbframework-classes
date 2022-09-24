<?php 

namespace Core;

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Utils\Replace;

class AppTest extends TestCase
{
    public function testNamespaces() {

        $saida_html = 'essa fruta é {azul}.';
        $Output = Replace::replace($saida_html, ['azul' => 'amarela']);

        $this->assertEquals('essa fruta é amarela.', $Output);

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
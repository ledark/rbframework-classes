<?php 

namespace Core;

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Utils\Replace;
use RBFrameworks\Core\App;

class AppTest extends TestCase
{
    public function notestApplicationStart() {
        $_SERVER["REQUEST_METHOD"] = "GET";
        try {
            $app = new App('tests/Samples/app/main-file.php');
            $app->run();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'Não foi possível iniciar a aplicação');
        }
        
    }
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
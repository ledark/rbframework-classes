<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Utils\Replace;
use RBFrameworks\Core\App;
use RBFrameworks\Core\Directory;
use RBFrameworks\Core\Assets\Stream;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Database\Form;
use Medoo\Medoo;
use RBFrameworks\Core\Database\Model\UserDadosMock;

//include(__DIR__.'/../Samples/Model/UserDados.php');

class ModelTest extends TestCase {

    public function testModel() {
        $dados = new UserDadosMock();
        $this->assertIsArray($dados->getModel());
        $this->assertArrayHasKey('cod', $dados->getModel());
    }

}
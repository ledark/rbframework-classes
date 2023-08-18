<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Template;
use RBFrameworks\Core\Database\Query;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\InputUser;

class InputUserTest extends TestCase {
        
    public function testWhenEmpty() {

        $data = (new InputUser())->getResult();

        $this->assertEmpty($data);
    }

    public function testWhenNotEmpty() {
        unset($_POST);
        $_POST['teste'] = '123';
        $data = (new InputUser())->getResult();

        $this->assertNotEmpty($data);
    }

    public function testCorrectKey() {
        unset($_POST);
        $_POST['teste1'] = '123';
        $_POST['teste2'] = '123';
        $data = (new InputUser())->getResult();

        $this->assertCount(2, $data, serialize($data));
        $this->assertArrayHasKey('teste1', $data);
    }

    
    public function testAssignedDefaultValue() {
        unset($_POST);
        $_POST['teste3'] = '123';
        $data = (new InputUser())->assigned('teste4', '456')->getResult();

        $this->assertCount(2, $data, serialize($data));
        $this->assertArrayHasKey('teste3', $data);        
        $this->assertArrayHasKey('teste4', $data);        
    }
        
}
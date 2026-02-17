<?php 

namespace RBFrameworks\Core\Database;

use RBFrameworks\Core\Interfaces\isCrudable;
use RBFrameworks\Core\Exceptions\DatabaseException as Exception;

class DatabaseAdapter {
    
    public function add(array $dados) {
        throw new Exception('Adapter Method --add not Implemented');
    }
    
    public function set(array $dados, array $keys) {
        throw new Exception('Adapter Method --set not Implemented');
    }
    
    public function upsert(array $dados, array $keys) {
        throw new Exception('Adapter Method --upsert not Implemented');
    }
    
    public function get(array $dados, array $criterias) {
        throw new Exception('Adapter Method --get not Implemented');
    }
    
    public function del(array $keys) {
        throw new Exception('Adapter Method --del not Implemented');
    }
    
}
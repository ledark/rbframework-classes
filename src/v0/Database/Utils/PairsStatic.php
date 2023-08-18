<?php

namespace RBFrameworks\Database\Utils;

abstract class PairsStatic {
    
    public function get(string $name, string $table = 'meta') {
        return (new \RBFrameworks\Database\Utils\Pairs($table))->get();
    }
    
    public function set(array $setter, string $table = 'meta') {
        (new \RBFrameworks\Database\Utils\Pairs($table))->setVarname(key($setter))->set(reset($setter));
    }
    
}
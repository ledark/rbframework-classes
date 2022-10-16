<?php 

namespace RBFrameworks\Core\Interfaces;

interface isCrudable {

    public function add(array $dados);
    
    public function set(array $dados, array $keys);
    
    public function upsert(array $dados, array $keys);
    
    public function get(array $dados, array $criterias);
    
    public function del(array $keys);

}


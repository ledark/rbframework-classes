<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace RBFrameworks\Interfaces;

/**
 *
 * @author Lenovo
 */
interface Crudable {
    
    public function add(array $dados);
    
    public function set(array $dados, array $keys);
    
    public function upsert(array $dados, array $keys);
    
    public function get(array $dados, array $criterias);
    
    public function del(array $keys);
    
}

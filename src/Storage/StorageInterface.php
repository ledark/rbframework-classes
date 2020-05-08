<?php 

namespace RBFrameworks\Storage;

interface StorageInterface {

    public function write(string $path, string $data) : void;

    public function read(string $path) : string;

    public function exists(string $path) : bool;

    public function remove(string $path) : void;
    
}
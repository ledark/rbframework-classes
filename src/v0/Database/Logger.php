<?php

namespace RBFrameworks\Database;

class Logger {
    
    const folder = 'log/';
    const filename = 'RBFrameworks.Database.Logger';
    public $pre, $sux;
    
    public function __construct() {
        $this->pre = date("Y-m-d H:i:s|");
        $this->sux = "\r\n";
    }

    public function addQueryError($query) {
        
        file_put_contents($this::folder.$this::filename, $this->pre.$query.$this->sux, FILE_APPEND);
        
    }
    
}

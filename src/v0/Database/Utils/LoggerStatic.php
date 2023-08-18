<?php

namespace RBFrameworks\Database\Utils;

abstract class LoggerStatic {
    
    public function add(string $message, string $group = 'info', array $context = [], string $table = 'logger') {
        (new \RBFrameworks\Database\Utils\Logger($table))->add($message, $group, $context);
    }
    
}
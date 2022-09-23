<?php

namespace RBFrameworks\Core\Exceptions;

class CoreTypeException extends \Exception
{
    public function __construct(string $message) {
        $message = $this->resolveMessage($message);
        parent::__construct($message);
    }

    private function resolveMessage(string $message): string {
        $message = str_replace('invalid format', 'possui formato inválido', $message);
        return $message;
    } 

}
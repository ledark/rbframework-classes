<?php

namespace RBFrameworks\Core\Response\Mock;

class ExceptionISO88591 extends \Exception
{
    public function __construct(string $message) {
        parent::__construct(utf8_decode($message));
    }
}

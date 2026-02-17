<?php

namespace RBFrameworks\Core\Response\Mock;

class ExceptionUTF8 extends \Exception
{
    public function __construct(string $message) {
        parent::__construct(utf8_encode($message));
    }
}

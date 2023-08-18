<?php 

namespace RBFrameworks\Core\Exceptions;

class DatabaseException extends \Exception {

    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public static function throw(string $message): void {
        throw new self($message);
    }

}
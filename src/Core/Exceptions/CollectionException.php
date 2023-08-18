<?php 

namespace RBFrameworks\Core\Exceptions;

class CollectionException extends DefaultException {

    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
<?php 

namespace RBFrameworks\Core\Exceptions;

use RBFrameworks\Core\Templates\Render;
use RBFrameworks\Core\Template;

class ClassNotFoundException extends DefaultException {

    public function __construct($message = "", $code = 0, \Exception $previous = null) {

        $templateException = new Template();
        $templateException = $templateException->renderPage(__DIR__.'/../Templates/debug/exceptions.html', [
            'title' => 'Route Exception',
            'message' => $message
        ]);


        parent::__construct($message, $code, $previous);
    }

}
<?php

namespace RBFrameworks\Core\Api\Responses;

use RBFrameworks\Core\Api\HandlerResponse;

class FileResponse extends HandlerResponse {
    
    public HandlerResponse $response;

    public function __construct(HandlerResponse $response = null) {
        if(is_null($response)) {
            $response = new HandlerResponse();
            $response
                ->setCharset('')  //canbe utf-8 | iso-8859-1 | empty
                ->setType('file') //canbe html|text|json|css|javascript|file|redirect|image
                ->setResult('path/to/file') //canbe mixed
                ->setResponseCode(200); //canbe int
        }
        $this->response = $response;
    }

    public function send() {
        if(!headers_sent()) {
            header('Content-Type: application/octet-stream');
        }
        readfile($this->response->getResult());
    }

}
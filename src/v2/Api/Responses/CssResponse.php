<?php

namespace RBFrameworks\Core\Api\Responses;

use RBFrameworks\Core\Api\HandlerResponse;

class CssResponse extends HandlerResponse {
    
    public HandlerResponse $response;

    public function __construct(HandlerResponse $response = null) {
        if(is_null($response)) {
            $response = new HandlerResponse();
            $response
                ->setCharset('utf-8')  //canbe utf-8 | iso-8859-1 | empty
                ->setType('css') //canbe html|text|json|css|javascript|file|redirect|image
                ->setResult('//maybe a file uri') //canbe mixed
                ->setResponseCode(200); //canbe int
        }
        $this->response = $response;
    }

    public function send() {
        if(!headers_sent()) {
            header('Content-Type: text/css'.$this->response->getCharset());
        }
        echo $this->response->getResult();
    }

}
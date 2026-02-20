<?php

namespace Framework\Router\Responses;

use Framework\Router\HandlerResponse;

class TextResponse extends HandlerResponse {

    public HandlerResponse $response;

    public function __construct(HandlerResponse $response = null) {
        if(is_null($response)) {
            $response = new HandlerResponse();
            $response
                ->setCharset('utf-8')  //canbe utf-8 | iso-8859-1 | empty
                ->setType('text') //canbe html|text|json|css|javascript|file|redirect|image
                ->setResult('hello world') //canbe mixed
                ->setResponseCode(200); //canbe int
        }
        $this->response = $response;
    }

    public function send() {
        if(!headers_sent()) {
            header('Content-Type: text/plain'.$this->response->getCharset());
        }
        echo $this->response->getResult();
    }

}
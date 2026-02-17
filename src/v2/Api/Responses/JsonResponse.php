<?php

namespace RBFrameworks\Core\Api\Responses;

use RBFrameworks\Core\Response;
use RBFrameworks\Core\Api\HandlerResponse;

class JsonResponse extends HandlerResponse {
    
    public HandlerResponse $response;

    public function __construct(HandlerResponse $response = null) {
        if(is_null($response)) {
            $response = new HandlerResponse();
            $response
                ->setCharset('utf-8')  //canbe utf-8 | iso-8859-1 | empty
                ->setType('json') //canbe html|text|json|css|javascript|file|redirect|image
                ->setResult([]) //canbe mixed
                ->setResponseCode(200); //canbe int
        }
        $this->response = $response;
    }

    public function send() {
        $forceEncodeUTF8 = isset($this->response->utf8) and is_bool($this->response->utf8) ? $this->response->utf8 : true;
        Response::json($this->response->getResult(), $forceEncodeUTF8, $this->response->getResponseCode());
    }
    
}
<?php

namespace Framework\Router\Responses;

use Framework\Router\HandlerResponse;

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
        if(!headers_sent()) header("Content-Type: application/json");
        http_response_code($this->response->getResponseCode());
        $content = $this->response->getResult();
        if($forceEncodeUTF8) {
            $content = encoding($content);
        }
		echo json_encode($content);
		exit();
    }

}
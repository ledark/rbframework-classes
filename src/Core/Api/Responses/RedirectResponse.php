<?php

namespace RBFrameworks\Core\Api\Responses;

use RBFrameworks\Core\Api\HandlerResponse;
use RBFrameworks\Core\Http;

class RedirectResponse extends HandlerResponse {
    
    public HandlerResponse $response;

    public function __construct(HandlerResponse $response = null) {
        if(is_null($response)) {
            $response = new HandlerResponse();
            $response
                ->setCharset('')  //canbe utf-8 | iso-8859-1 | empty
                ->setType('redirect') //canbe html|text|json|css|javascript|file|redirect|image
                ->setResult('path/to/absolute/uri') //canbe mixed
                ->setResponseCode(200); //canbe int
        }
        $this->response = $response;
    }

    public function send() {
        if(!headers_sent()) {
			$result = $this->response->getResult();
			$result = str_replace('{httpSite}', Http::getSite(), $result);
            header('Location: '.$result);
        }
    }

}
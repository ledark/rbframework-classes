<?php

namespace RBFrameworks\Core\Api\Responses;

use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Api\HandlerResponse;

class ImageResponse extends HandlerResponse {
    
    public HandlerResponse $response;

    public function __construct(HandlerResponse $response = null) {
        if(is_null($response)) {
            $response = new HandlerResponse();
            $response
                ->setCharset('')  //canbe utf-8 | iso-8859-1 | empty
                ->setType('image') //canbe html|text|json|css|javascript|file|redirect|image
                ->setResult('path/to/image') //canbe mixed
                ->setResponseCode(200); //canbe int
        }
        $this->response = $response;
    }

    public function send() {
        $image = new File($this->response->getResult());
        File::readFile($image->getFilePath());   
    }

}
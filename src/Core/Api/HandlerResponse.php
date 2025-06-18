<?php

namespace RBFrameworks\Core\Api;

use RBFrameworks\Core\Api\InterfaceResponse;
use RBFrameworks\Core\Api\Responses\HtmlResponse;
use RBFrameworks\Core\Api\Responses\TextResponse;
use RBFrameworks\Core\Api\Responses\JsonResponse;
use RBFrameworks\Core\Api\Responses\CssResponse;
use RBFrameworks\Core\Api\Responses\JavascriptResponse;
use RBFrameworks\Core\Api\Responses\FileResponse;
use RBFrameworks\Core\Api\Responses\RedirectResponse;
use RBFrameworks\Core\Api\Responses\ImageResponse;

class HandlerResponse implements InterfaceResponse {

    private string $charset = 'utf-8';
    private string $type = 'text';
    private $result = null;
    public bool $utf8;

    //canbe utf-8 | iso-8859-1 | empty
    public function setCharset(string $charset):HandlerResponse {
        $this->charset = $charset;
        return $this;
    }

    //canbe html|text|json|css|javascript|file|redirect|image
    public function setType(string $type):HandlerResponse {
        $this->type = $type;
        return $this;
    }

    //canbe mixed
    public function setResult($result):HandlerResponse {
        $this->result = $result;
        return $this;
    }

    //canbe int
    public function setResponseCode(int $responseCode):HandlerResponse {
        return $this;
    }

    public function getResult() {
        return $this->result;
    }

    public function getResponseCode():int {
        return 200;
    }

    public function getType():string {
        return $this->type;
    }

    public function getCharset():string {
        return $this->charset;
    }

    public function asText() {
        $this->type = 'text';
        return $this;
    }
    public function send() {
        switch($this->getType()) {

            case 'html':
                (new HtmlResponse($this))->send(); 
            break;
            case 'text':
                (new TextResponse($this))->send(); 
            break;
            case 'json':
                (new JsonResponse($this))->send(); 
            break;
            case 'css':
                (new CssResponse($this))->send(); 
            break;
            case 'javascript':
                (new JavascriptResponse($this))->send(); 
            break;
            case 'file':
                (new FileResponse($this))->send(); 
            break;
            case 'redirect':
                (new RedirectResponse($this))->send(); 
            break;
            case 'image':
                (new ImageResponse($this))->send(); 
            break;
        }
    }
}

/*
if(is_array($result) or $responseType == 'json') {
    
    
} else if($responseType == 'html') {
    if(!headers_sent()) {
        header('Content-Type: text/html'.$charset());
    }
    echo $result;
} else if($responseType == 'css') {
    if(!headers_sent()) {
        header('Content-Type: text/css'.$charset());
    }
    echo $result;
} else if($responseType == 'javascript') {
    if(!headers_sent()) {
        header('Content-Type: text/javascript'.$charset());
    }
    echo $result;                                                        
} else if($responseType == 'file') {
    if(!headers_sent()) {
        header('Content-Type: application/octet-stream');
    }
    readfile($result);
} else if($responseType == 'image') {
    $image = new File($result);
    File::readFile($image->getFilePath());                            
} else if($responseType == 'redirect') {
    if(!headers_sent()) {
        header('Location: '.$result);
    }

    */
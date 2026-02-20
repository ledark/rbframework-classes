<?php

namespace Framework\Router\Responses;

use Framework\Router\HandlerResponse;
use Framework\Utils\Htmx;
use Framework\Utils\Htmx\HtmxComponent;

class HtmxResponse extends HandlerResponse {

    public HandlerResponse $response;

    public function __construct(HandlerResponse $response = null) {
        if(is_null($response)) {
            $response = new HandlerResponse();
            $response
                ->setCharset('utf-8')  //canbe utf-8 | iso-8859-1 | empty
                ->setType('html') //canbe html|text|json|css|javascript|file|redirect|image
                ->setResult('ExampleStaticComponent')
                ->setResponseCode(200); //canbe int
        }
        $this->response = $response;
    }

    public function send() {
        if(!headers_sent()) {
            header('Content-Type: text/html'.$this->response->getCharset());
        }
        $componenetName = !is_null($this->response->getResult()) ? $this->response->getResult() : '';
        $component = new HtmxComponent('', $componenetName);
        echo $component;
    }

}
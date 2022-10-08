<?php

namespace RBFrameworks\Core\Http;

interface HttpRequestInterface
{
    //Basic for any Handler: uri, method,  options, client
    public function getUri():string;
    public function getClient():object;
    public function getMethod():string;
    public function getOptions():array;

    /*
    public function setUri(string $uri):object;
    public function setMethod(string $method):object;
    public function setFormParams($form_params):object;
    public function setResponse(string $response):object;
    */

    //For Response
    public function request();
    
}

<?php

namespace Core\Http;

interface HttpRequestInterface
{
    //Basic for any Handler: uri, method,  options, client
    public function getUri():string;
    public function getClient():object;
    public function getMethod():string;
    public function getOptions():array;

    //For Response
    public function request();
    
}

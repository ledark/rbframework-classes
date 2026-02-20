<?php

namespace Framework\Router;

use Framework\Router\HandlerResponse;

interface InterfaceResponse {

    //Setters
    public function setCharset(string $charset):HandlerResponse;
    public function setType(string $type):HandlerResponse;
    public function setResult($result):HandlerResponse;
    public function setResponseCode(int $responseCode):HandlerResponse;

    //Getters
    public function getCharset():string;
    public function getType():string;
    public function getResult();
    public function getResponseCode():int;

    //Send
    public function send();

}
<?php

namespace Framework\Router;

use Framework\Router\InterfaceResponse;
use Framework\Router\Responses\HtmlResponse;
use Framework\Router\Responses\TextResponse;
use Framework\Router\Responses\JsonResponse;
use Framework\Router\Responses\CssResponse;
use Framework\Router\Responses\JavascriptResponse;
use Framework\Router\Responses\FileResponse;
use Framework\Router\Responses\RedirectResponse;
use Framework\Router\Responses\ImageResponse;
use Framework\Router\Responses\HtmxResponse;

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
            case 'htmx':
                (new HtmxResponse($this))->send();
            break;
        }
    }
}
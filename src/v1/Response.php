<?php

namespace Framework;

class Response {

    public static function json($data, int $statusCode = 200) {
        return new \Framework\Router\Responses\JsonResponse($data, $statusCode);
    }

    public static function text(string $content, int $statusCode = 200) {
        return new \Framework\Router\Responses\TextResponse($content, $statusCode);
    }

    public static function js(string $content, int $statusCode = 200) {
        return new \Framework\Router\Responses\JavascriptResponse($content, $statusCode);
    }

    public static function css(string $content, int $statusCode = 200) {
        return new \Framework\Router\Responses\CssResponse($content, $statusCode);
    }

    public static function xml(string $content, int $statusCode = 200) {
        return new \Framework\Router\Responses\TextResponse($content, $statusCode);
    }

    public static function html(string $content, int $statusCode = 200) {
        return new \Framework\Router\Responses\HtmlResponse($content, $statusCode);
    }

}

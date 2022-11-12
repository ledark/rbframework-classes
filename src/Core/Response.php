<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Utils\Encoding;

class Response
{
    public static function json(array $arr, bool $forceEncodeUTF8 = false, int $statusCode = 200) {
		if(!headers_sent()) header("Content-Type: application/json"); 
        self::responseCode($statusCode);
        if($forceEncodeUTF8) Encoding::DeepEncode($arr);
		echo json_encode($arr);
		exit();
    }

	public static function text(string $text, int $statusCode = 200) {
		if(!headers_sent()) header("Content-Type: text/plain"); 
        self::responseCode($statusCode);
		echo $text;
		exit();
	}
    
    public static function js(string $text, int $statusCode = 200) {
		if(!headers_sent()) header("Content-Type: text/javascript"); 
        self::responseCode($statusCode);
		echo $text;
		exit();
	}

    public static function css(string $cssContent, int $statusCode = 200) {
		if(!headers_sent()) header("Content-Type: text/css");
		self::responseCode($statusCode);
		echo $cssContent;
		exit();
	}

	public static function xml(string $xml, int $statusCode = 200) {
		if(!headers_sent()) header("Content-Type: application/xml");
        self::responseCode($statusCode);
		echo $xml;
        exit();
	}    

	public static function html(string $htmlContent, int $statusCode = 200) {
		if(!headers_sent()) header("Content-Type: text/html");
		self::responseCode($statusCode);
		echo $htmlContent;
		exit();
	}

    private static function responseCode(int $statusCode) {
        http_response_code($statusCode);
        if(isset(self::errorCodes()[$statusCode]) and !headers_sent()) {
            header(self::errorCodes()[$statusCode]);
        }
    }

    private static function errorCodes():array {
        return [
            403 => "HTTP/1.1 403 Forbidden",
            404 => "HTTP/1.1 404 Not Found",
            500 => "HTTP/1.1 500 Internal Server Error",
            302 => "HTTP/1.1 302 Redirect",
            301 => "HTTP/1.1 301 Moved Permanently",
        ];
    }

}

//Polyfill PHP<7.3
if (!function_exists('is_countable')) {
    function is_countable($var) {
        return (is_array($var) || $var instanceof \Countable);
    }
}
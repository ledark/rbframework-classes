<?php

namespace RBFrameworks;

use RBFrameworks\Core\Utils\Encoding;
use Countable;

class Response {
    
    public static function obClean() {
        return ob_get_clean();
    }


    public static function toUtf8(&$mixed) {
        Encoding::DeepEncode($mixed);
    }
    
    public static function json($mixed) {
        
        if(!headers_sent()) header("Content-Type: application/json"); 
        
        if(is_string($mixed)) {
            echo json_encode($mixed);
            exit();
        }
            
        if($mixed instanceof \Traversable) {
            $mixed = iterator_to_array($mixed);
        }
        
        if(!is_object($mixed) and !is_countable($mixed)) $mixed = array();
        
        echo json_encode($mixed);
        exit();
            
        /*
        
        if(!$returnError) {
		if(!count($arr)) $arr = array(
			'mensagem'	=>	'Erro desconhecido.'
		,	'addclass'	=>	'alert alert-danger'
		);
        } else {
        }
		plugin('utf8_encode_deep');
		utf8_encode_deep($arr);
		echo json_encode($arr);
		die();
        */
    }
    
    public static function redir($uri) {
        
        if(substr($uri, 0, 1) == '/') {
            $uri = (substr(HTTPSITE, -1) == '/') ? HTTPSITE. substr($uri, 1): HTTPSITE.$uri;
        }
        
        header("Location: $uri", true);
        exit();
    }
    
    public static function text($text) {
        header("Content-Type: text/plain"); 
        switch(Encoding::detect($text)) {
            case 'ISO-8859-1':
                $text = utf8_encode($text);
            break;
        }
        echo $text;
    }

    public static function xml($text) {
        header("Content-Type: application/xml"); 
        switch(Encoding::detect($text)) {
            case 'ISO-8859-1':
                $text = utf8_encode($text);
            break;
        }
        echo $text;
    }
}

//Polyfill PHP<7.3
if (!function_exists('is_countable')) {
    function is_countable($var) {
        return (is_array($var) || $var instanceof Countable);
    }
}
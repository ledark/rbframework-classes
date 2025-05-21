<?php 

namespace Framework;

use GuzzleHttp\Client;
use Framework\Traits\HttpStaticTrait;

class Http {

    public $client;

    public function __construct() {
        $this->client = new Client();
    }

    use HttpStaticTrait;

    public static function getDomain():string {
        return Config::get('server.http_host');
    }

    public static function getHost():string {
        $_SERVER['REQUEST_SCHEME'] = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
        if(substr($_SERVER['REQUEST_SCHEME'], -3) != '://') $_SERVER['REQUEST_SCHEME'] = $_SERVER['REQUEST_SCHEME'] . '://';
        $_SERVER['REQUEST_SCHEME'] = str_replace(':', '', $_SERVER['REQUEST_SCHEME']);
        $_SERVER['REQUEST_SCHEME'] = str_replace('/', '', $_SERVER['REQUEST_SCHEME']);
        $_SERVER['REQUEST_SCHEME'] = $_SERVER['REQUEST_SCHEME']. '://';
        return $_SERVER['REQUEST_SCHEME'] . self::getDomain();
    }

    public static function getSite():string {
        $script = basename($_SERVER['SCRIPT_NAME']); //framework aka index.php
        return self::getHost(). substr($_SERVER['SCRIPT_NAME'], 0, strlen($script)*-1); //remove index.php
    }

    public static function httpSite():string {
        return Config::assigned('server.base_url', self::getSite());
    }

    public static function redir(string $uri):void {
        $uri = str_replace('{httpSite}', self::httpSite(), $uri);
        if(headers_sent()) {
            echo '<script type="text/javascript">window.location.href = "'.$uri.'";</script>';
            echo '<noscript><meta http-equiv="refresh" content="0;url='.$uri.'" /></noscript>';
            exit();
        }
        header('Location: ' . $uri);
        exit();
    }

    public static function isAbsolute(string $uri):bool {
        if(substr($uri, 0, 2) == '//') return true;
        if(substr($uri, 0, 7) == 'http://') return true;
        if(substr($uri, 0, 8) == 'https://') return true;
        return false;
    }

}
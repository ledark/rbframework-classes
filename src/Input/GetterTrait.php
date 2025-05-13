<?php

namespace Framework\Input;

use Framework\Session;

trait GetterTrait {

    public function phpInput():array {
        $request_file = file_get_contents("php://input");

        if($request_file !== false) {
            $request_json = json_decode($request_file);
            if(is_null($request_json)) return [];
            return get_object_vars($request_json);
        } else {
            return [];
        }
    }

    public function phpPost():array {
        return $_POST??[];
    }

    public function get():array {
        return $this->phpGet();
    }
    public function phpGet():array {
        return $_GET??[];
    }

    public function phpSessionGet(string $key, $default = null) {
        return (isset($_SESSION) and isset($_SESSION[$key])) ? $_SESSION[$key] : $default;
    }
    public function phpSession():array {
        return isset($_SESSION) ? $_SESSION : array();
    }

    public function phpCreateHeaders(array $headers):bool {
        if(function_exists('apache_request_headers')) return false;
        $this->headers = array_merge($this->phpRequestHeaders(), $headers);
        return true;
    }

    public function phpRequestHeaders():array {
        if(function_exists('apache_request_headers')) return apache_request_headers();
        if(is_array($this->headers)) return $this->headers;
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach($_SERVER as $key => $val) {
          if( preg_match($rx_http, $key) ) {
            $arh_key = preg_replace($rx_http, '', $key);
            $rx_matches = array();
            // do some nasty string manipulations to restore the original letter case
            // this should work in most cases
            $rx_matches = explode('_', $arh_key);
            if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
              foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
              $arh_key = implode('-', $rx_matches);
            }
            $arh[$arh_key] = $val;
          }
        }
        $this->headers = $arh;
        return( $arh );       
    }

}


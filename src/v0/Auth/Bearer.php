<?php

namespace RBFrameworks\Auth;

use RBFrameworks\Core\Config;

class Bearer {
    
    private $tokens = [];
    
    public function setBearerTokens(string $token) {
        $token = trim($token);
        if(substr($token, 0, 7) == 'Bearer ') {
            $token = substr($token, 7);
        }
        $this->tokens[] = "Bearer {$token}";
        return $this;
    }

    public function getBearerTokens():array {
        $bearers = Config::assigned('auth.bearers', []);
        foreach($bearers as $bearer) {
            $this->setBearerTokens($bearer);
        }
        return $this->tokens;
    }
    
    public function restricted() {
        if(!isset(apache_request_headers()['Authorization'])) {
            http_response_code(403);
            exit();
        } else {
            if(!in_array(apache_request_headers()['Authorization'], $this->getBearerTokens())) {
                http_response_code(401);
                exit();
            }
        }
    }

    public static function Restrict() {
        if(!isset(apache_request_headers()['Authorization'])) {
            http_response_code(403);
            exit();
        } else {
            if(!in_array(apache_request_headers()['Authorization'], self::getBearerTokens())) {
                http_response_code(401);
                exit();
            }
        }
    }
    
}
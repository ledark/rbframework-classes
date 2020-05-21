<?php

namespace RBFrameworks\Auth;

class Bearer {
    
    private $tokens = [];
    
    public function setBearerTokens(string $token) {
        $this->tokens[] = "Bearer {$token}";
        return $this;
    }

    public function getBearerTokens():array {
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
    
}
<?php

namespace RBFrameworks\Auth;

/**
 * Not Implemented
 */
class Browser {
    
    public function run() {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Texto enviado caso o usu�rio clique no bot�o Cancelar';
            exit;
        } else {
            echo "<p>Ol�, {$_SERVER['PHP_AUTH_USER']}.</p>";
            echo "<p>Voc� digitou {$_SERVER['PHP_AUTH_PW']} como sua senha.</p>";
        }        
    }
    
}

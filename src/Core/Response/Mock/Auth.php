<?php

namespace RBFrameworks\Core\Response\Mock;

class Auth implements Mockable {

    public static function getMockError(string $message = "Login ou Senha Inválidos"):array {
        $mock = self::getMock();
        unset($mock[0]);
        $mock['error'] = true;
        $mock['errorv'] = $message;
        return $mock;
    }
    public static function getMockSuccess(string $message = "Autenticado!"):array {
        $mock = self::getMock();
        $mock['error'] = false;
        $mock['errorv'] = "";
        $mock[0]['success'] = 'ok';
        return $mock;
    }
    public static function getMock():array {
        $mock = [
            'error' => true, 
            'errorv' => "Erro Desconhecido"
        ];
        $mock[0] = ['success' => 'none'];
        return $mock;
    }
    public static function throwSuccess(string $message):array {
        return self::getMockSuccess($message);
    }
    public static function throwError(string $message):array {
        return self::getMockError($message);
    }
}
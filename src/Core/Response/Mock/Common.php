<?php

namespace RBFrameworks\Core\Response\Mock;

use RBFrameworks\Core\Response\ResponseJson;
use RBFrameworks\Core\Plugin;

class Common implements Mockable {

    public static function getMockError($message = "error!", array $merge = [], bool $utf8_encode = true):array {
        if($utf8_encode === true) $message = utf8_encode($message);
        if($utf8_encode === false) $message = utf8_decode($message);
        return array_merge($merge, self::getMock(), [
            'message' => $message,
            'code' => 510,
            'error' => true,
        ]);
    }
    public static function getMockSuccess($message = "success!", array $merge = [], bool $utf8_encode = true):array {
        if($utf8_encode === true) $message = utf8_encode($message);
        if($utf8_encode === false) $message = utf8_decode($message);
        return array_merge($merge, self::getMock(), [
            'message' => $message,
            'code' => 210,
        ]);
    }
    public static function getMock():array {
        $mock = [
            'message' => "no message",       
            'code' => 200,
            'error' => false,
        ];
        return $mock;
    }

    //Depends on Mocks\ResponseCommon
    public static function success(string $message, array $additionalData = [], bool $forceEncodeUTF8 = false) {
        if(is_object($additionalData)) $additionalData = ['object' => (array) $additionalData ];
        if(is_string($additionalData)) $additionalData = ['string' => $additionalData ];
        if(is_int($additionalData)) $additionalData = ['int' => $additionalData ];    
        self::json(self::getMockSuccess($message, $additionalData), $forceEncodeUTF8);
    }

    //Depends on Mocks\ResponseCommon
    public static function error(string $message, array $additionalData = [], bool $forceEncodeUTF8 = false) {
        if(is_object($additionalData)) $additionalData = ['object' => (array) $additionalData ];
        if(is_string($additionalData)) $additionalData = ['string' => $additionalData ];
        if(is_int($additionalData)) $additionalData = ['int' => $additionalData ];
        self::json(self::getMockError($message, $additionalData), $forceEncodeUTF8);
    }

    public static function json(array $dados, bool $forceEncodeUTF8 = false) {
        ResponseJson::json($dados, $forceEncodeUTF8);
    }

}
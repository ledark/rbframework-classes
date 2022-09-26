<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Debug;

abstract class Auth {

    public static function generateToken(string $salt = ''):string {
        return self::getSecret($salt).'-'.self::getEncriptIP().'-'.self::getUniqID();
    }

    public static function generateTokenConditional(string $salt = ''):string {
        Plugin::load('session');
        $token = session_admin_get_token();
        return self::checkToken($token, $salt) ? $token : self::generateToken($salt);
    }

    public static function checkToken(string $token, string $salt = ''):bool {
        $tokenParts = explode('-', $token);
        $secret = $tokenParts[0];
        $userIP = $tokenParts[1];
        $uniqID = self::resolveUniqID($tokenParts[2]);
        if(
            $secret == self::getSecret($salt) and
            preg_match_all( "/[0-9]/", $userIP ) >= 8  and
            self::isValidTimeStamp($uniqID) and
            date('Y', $uniqID) >= 2021 and
            date('m', $uniqID) >= 01 
            ) return true;
        return false;
    }

    private static function desmemberToken(string $token): array {
        $tokenParts = explode('-', $token);
        return [
            'secret' => $tokenParts[0],
            'userIP' => $tokenParts[1],
            'uniqID' => self::resolveUniqID($tokenParts[2])
        ];
    }

    public static function extractIp(string $token):string {
        $userIP = self::desmemberToken($token)['userIP'];
        return self::resolveEncriptedIP($userIP);
    }
    public static function extractDate(string $token):int {
        $uniqID = self::desmemberToken($token)['uniqID'];
        return self::isValidTimeStamp($uniqID) ? intval($uniqID) : 0;
    }

    private static function getSecret(string $salt = ''):string {
        return empty($salt) ? md5('nasnuvens') : md5($salt);
    }

    private static function getUniqID():string {
        return strtr( (string) time(), implode('', self::getNumberStringMap(true)), implode('', self::getNumberStringMap(false)) );
    }

    private static function resolveUniqID(string $uniqID):string {
        return strtr( $uniqID, implode('', self::getNumberStringMap(false)), implode('', self::getNumberStringMap(true)) );
    }

    private static function getNumberMap(bool $flipped = false): array {
        $arr = ['0' => '3', '1' => '6', '2' => '8', '3' => '4', '4' => '9', '5' => '2', '6' => '0', '7' => '5', '8' => '7', '9' => '1', '.' => 'b', ':' => 'a'];
        return ($flipped) ? array_flip($arr): $arr;
    }

    private static function getNumberStringMap(bool $flipped = false): array {
        $arr = ['0' => 'g', '1' => 'a', '2' => 'w', '3' => 'n', '4' => 'i', '5' => 'l', '6' => 'y', '7' => 'f', '8' => 'c', '9' => 'r'];
        return ($flipped) ? array_flip($arr): $arr;
    }

    public static function getEncriptIP():string {
        $encriptedIP = '';
        $userIP = $_SERVER['REMOTE_ADDR'] ?? '192.168.0.1';
        for($i=0; $i<=strlen($userIP); $i++) {
            if(isset($userIP[$i])) {
                if(in_array($userIP[$i], array_keys(self::getNumberMap()))) {
                    $char = self::getNumberMap()[$userIP[$i]];
                    $char = $char === '.' ? self::generateRandomString() : $char; 
                    $encriptedIP.= $char;
                }
            }
        }
        return $encriptedIP;       
    }

    public static function resolveEncriptedIP(string $userIP):string {
        $decriptedIP = '';
        for($i=0; $i<=strlen($userIP); $i++) {
            if(isset($userIP[$i])) {
                if(in_array($userIP[$i], array_keys(self::getNumberMap(true)))) {
                    $char = isset( self::getNumberMap(true)[$userIP[$i]]) ? self::getNumberMap(true)[$userIP[$i]] : '.';
                    $char = $char === 'a' ? ':' : $char;
                    $decriptedIP.= $char;
                }
            }
        }
        return $decriptedIP;
    }

    private static function generateRandomString($length = 1):string {
        $characters = 'bcdefghijklmnopqrstuvwxyzBCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private static function isValidTimeStamp($timestamp):bool {
        return ((string) (int) $timestamp === $timestamp) 
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }    

    /**
     * Em qualquer requisição, seja GET ou POST ou qualquer outra use o código:
     * \RBFrameworks\Core\Auth::hasBearerToken('qualquer-string');
     * 
     * Essa função verifica se foi enviado o determinado Bearer Token igual a qualquer-string, e devolve sim se verdadeiro.
     * Útil para testes de aplicações que você pode amazenar essa string nelas.
     * 
     * Se você não passar essa "qualquer-string" no seu código, então a função procura por um token válido definido no arquivo de configuração auth.bearers
     */
    public static function hasBearerToken(string $token = null):bool {
        $hasValidBearerToken = false;

        $apache_request_headers = function():array {
            if(function_exists('apache_request_headers')) return apache_request_headers();
            return (new Input())->phpRequestHeaders();
        };

        if(!isset($apache_request_headers()['Authorization'])) return false;
        if(is_null($token)) {
            $tokens = Config::get('auth.bearers');
            foreach($tokens as $token) {
                if($apache_request_headers()['Authorization'] == "Bearer {$token}") $hasValidBearerToken = true;
            }
            $token = '';
        } else {
            $hasValidBearerToken = ($apache_request_headers()['Authorization'] == "Bearer {$token}") ? true : false;
        }
        Debug::log($token, [$hasValidBearerToken], $apache_request_headers()['Authorization'], null , 1 );
        return $hasValidBearerToken;
    }

}
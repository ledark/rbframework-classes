<?php

namespace RBFrameworks\Core\Response\Mock;

interface Mockable {

    public static function getMockError(string $message = "ErrorMessage"):array ;
    public static function getMockSuccess(string $message = "Success"):array ;
    public static function getMock():array ;
}
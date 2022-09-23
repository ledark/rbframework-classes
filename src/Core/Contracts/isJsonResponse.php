<?php

namespace RBFrameworks\Core\Contracts;

interface isJsonResponse
{
    public static function json(array $dados, bool $forceEncodeUTF8):void;
}
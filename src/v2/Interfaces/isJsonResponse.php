<?php

namespace RBFrameworks\Core\Interfaces;

interface isJsonResponse
{
    public static function json(array $dados, bool $forceEncodeUTF8):void;
}
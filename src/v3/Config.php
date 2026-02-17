<?php

namespace RBFrameworks;

class Config {
    public static function get(string $key, $default = null) {
        return collection($key, $default);
    }
}
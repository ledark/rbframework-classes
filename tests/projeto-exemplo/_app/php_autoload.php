<?php

use RBFrameworks\Autoload;

if(!function_exists('get_root_path')) {
    throw new \Exception('get_root_path function not found. Create this function in your project.');
}

Autoload::start();
Autoload::forceHttps();
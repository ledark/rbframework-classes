<?php

//Errors Control
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);
set_time_limit(60*60);

$include = function(string $path) {
    if(file_exists($path)) {
        include_once $path;
    }
};

function get_root_path(string $sufix = ""):string {
    $rootPath = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . ltrim($sufix, '/');
    return $rootPath;
}

// Define collection path for tests BEFORE including _include.php
if(!function_exists('get_collection_path')) {
    function get_collection_path(): string {
        return __DIR__ . '/../collection/';
    }
}

if(!function_exists('get_collection_dir')) {
    function get_collection_dir(): string {
        return __DIR__ . '/../collection/';
    }
}

// Define functions_dir for Plugin::load to work
if(!function_exists('get_functions_dir')) {
    function get_functions_dir(): string {
        return __DIR__ . '/../collection/';
    }
}

$include(__DIR__ . '/../../vendor/autoload.php');
$include(__DIR__.'/../../_include.php');

// Set default config for tests
if (!function_exists('config')) {
    function config(string $configName, $default = null, bool $overwrite = false) {
        return $default;
    }
}

register_shutdown_function(function () {

   // echo "\r\n";
   // echo collection('server.base_uri')."\r\n";
   // echo collection('server.base_uri')."api/debug/errorlog/clear\r\n";
   // echo date('Y-m-d H:i:s')."\r\n";
   // echo "\r\n";
});

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
    $rootPath = realpath(__DIR__ . '/../../').DIRECTORY_SEPARATOR.ltrim($sufix, '/');
    return $rootPath;
}

$include(__DIR__ . '/../../vendor/autoload.php');
$include(__DIR__.'/../../_include.php');

register_shutdown_function(function () {

   // echo "\r\n";
   // echo collection('server.base_uri')."\r\n";
   // echo collection('server.base_uri')."api/debug/errorlog/clear\r\n";
   // echo date('Y-m-d H:i:s')."\r\n";
   // echo "\r\n";
});
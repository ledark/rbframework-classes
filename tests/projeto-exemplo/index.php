<?php

use RBFrameworks\Autoload;

//GARANTA QUE OS INCLUDES DO COMPOSER SEJAM CARREGADOS

/*
require_once "_app/class/composer/autoload.php";
require_once "_app/class/composer/ledark/rbframeworks/_include.php";
*/

if(!function_exists('get_root_path')) {
    throw new \Exception('get_root_path function not found. Create this function in your project.');
}

//DEFINA A FUNÇÃO get_root_path() PARA CADA PROJETO

/*
function get_root_path(string $sufix = ""):string {
    $rootPath = realpath(__DIR__ . '/').DIRECTORY_SEPARATOR.ltrim($sufix, '/');
    return $rootPath;
}
*/

try {

    Autoload::start();
    Autoload::forceHttps();
    Autoload::routeMiddleware();
    Autoload::routeDirectFiles();
    Autoload::routeApi(function($request_file, $router) {
        header('HTTP/1.0 404 Not Found');
        echo '404 Not Found: '.$request_file;
        echo $router->getCurrentUri();
        exit();
    });

} catch(\Throwable $e) {
    header("HTTP/1.0 501 Not Implemented");
    echo $e->getMessage();
    exit();
}
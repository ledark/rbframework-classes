<?php

use RBFrameworks\Autoload;
use RBFrameworks\BladeOne;
use RBFrameworks\Core\Session;

//error_reporting(0);

require_once get_root_path("_app/class/composer/autoload.php");
require_once get_root_path("_app/class/composer/ledark/rbframeworks/_include.php");

function get_root_path(string $sufix = ""): string
{
    $rootPath = realpath(__DIR__ . '/') . DIRECTORY_SEPARATOR . ltrim($sufix, '/');
    return $rootPath;
}

try {

    new Session();

    Autoload
        ::start()
        ::forceHttps()
        ::loadFunctions(['rbframeworks', 'query'])
        ::routeMiddleware()
        ::routeDirectFiles()
        ::routeApi(function ($router) {
        $router
            ->redirectWhenEmpty()
            ->serveFile('views/')
            ->throw404Page(BladeOne::render('404', ['router' => $router->router->getCurrentUri()]))
            ;
    });

}
catch (\Throwable $e) {
    header("HTTP/1.0 501 Not Implemented");
    echo $e->getMessage();
    exit();
}
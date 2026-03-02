<?php

namespace RBFrameworks;

use DirectoryIterator;
use RBFrameworks\Core\Api;
use RBFrameworks\Core\Types\File;

class Autoload
{

    public static function start(): self
    {
        spl_autoload_register(['RBFrameworks\Autoload', 'loadClass']);
        set_include_path(get_include_path() . PATH_SEPARATOR . get_root_path());
        return new self;

    }

    public static function forceHttps(): self
    {
        if (collection('server.force_https')) {
            if (!isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') {
                header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
        return new self;
    }

    public static function loadFunction(string $functionName): self
    {
        foreach (collection('location.functions') as $functionFile) {
            $functionFile = str_replace('[FUNCTION_NAME]', $functionName, $functionFile);
            if ($functionName === '*') {
                $functionDirectory = str_replace('/*.php', '', $functionFile);
                foreach (new DirectoryIterator($functionDirectory) as $fileinfo) {
                    if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
                        self::loadFunction(basename($fileinfo->getFilename(), '.php'));
                    }
                }
            }
            if (function_exists($functionName)) {
                return new self;
            }
            else
                if (file_exists($functionFile)) {
                    require_once $functionFile;
                }
        }
        return new self;
    }

    public static function loadFunctions(array $functionNames): self
    {
        foreach ($functionNames as $functionName) {
            self::loadFunction($functionName);
        }
        return new self;
    }

    public static function loadClass(string $className)
    {
        if (!class_exists($className)) {

            $className = (strpos($className, '\\') !== false) ? str_replace('\\', '/', $className) : $className;

            foreach (collection('location.class_autoload') as $inc) {

                $inc = str_replace('[CLASS_NAME]', $className, $inc);
                if (file_exists(get_root_path($inc))) {
                    include($inc);
                    return;
                }
            }
        }
    }

    public static function routeMiddleware(): self
    {
        foreach (collection('routes.middleware', []) as $forbidden_path => $forbidden_callback) {
            $forbidden_callback = (is_callable($forbidden_callback)) ? $forbidden_callback : function () {
                header('HTTP/1.0 403 Forbidden');
                exit();
            };
            if (strpos($_SERVER['REQUEST_URI'], $forbidden_path) !== false) {
                $forbidden_callback();
            }
        }
        return new self;
    }

    public static function routeDirectFiles(): self
    {
        if (file_exists($_SERVER['REQUEST_URI'])) {
            $extension = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION);
            if (in_array($extension, collection('routes.direct_files', ['css', 'js', 'json']))) {
                header('Cache-Control: max-age=31536000');
                header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
                File::readFile($_SERVER['REQUEST_URI']);
                exit();
            }
        }
        return new self;
    }

    public static function routeApi(?callable $fn404): void
    {
        $router = new Api();
        foreach (collection('routes.autoload', []) as $route_dir => $route_namespace) {
            $glob_routerdir = glob($route_dir . '/*.php');
            if (!is_array($glob_routerdir)) {
                foreach (new DirectoryIterator($route_dir) as $fileinfo) {
                    if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
                        $glob_routerdir[] = $fileinfo->getPathname();
                    }
                }
            }
            foreach ($glob_routerdir as $route_file) {
                $full_namespace = '\\' . trim($route_namespace, '\\') . '\\' . basename($route_file, '.php');
                $router->addNamespace($full_namespace);
            }
        }

        if (is_callable($fn404)) {
            $router->fn404 = function () use ($router, $fn404) {
                if (strpos('index.php', $_SERVER['REQUEST_URI']) !== false) {
                    $request_file = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']) - strlen('index.php'));
                }
                else {
                    $request_file = $_SERVER['REQUEST_URI'];
                }

                if (file_exists($request_file)) {
                    $extension = pathinfo($request_file, PATHINFO_EXTENSION);
                    if (in_array($extension, collection('routes.direct_files', ['css', 'js', 'json']))) {
                        if (in_array($extension, collection('cache.direct_files', ['css']))) {
                            header('Cache-Control: max-age=31536000');
                            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
                        }
                        File::readFile($request_file);
                        exit();
                    }
                }

                $fn404($router);
            };
        }

        $router->run();
    }
}
<?php


try {
    include('_app/php_autoload.php');
    include('_app/php_functions.php');
    include('_app/config.php');
    include('_app/php_router.php');
} catch(Throwable $e) {
    header("HTTP/1.0 501 Not Implemented");
    echo $e->getMessage();
    exit();
}

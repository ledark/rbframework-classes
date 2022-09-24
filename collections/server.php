<?php 
return [
    'http_host' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'http://localhost/',
    'server' => [
        'baseUrl' => 'http://localhost/v1'
    ],    
    'php' => [
        'version' => 7.4,
    ],
    'framework' => [
        'version' => 99.6
    ],    
];
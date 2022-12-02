<?php 
return [
    'base_url' => 'http://localhost/v1',

    //Used in RBFrameworks\Core\Http\Guzzle
    'guzzle_preferences' => [
        'base_url' => '',
        'cookies' => true,
        'headers' => [
            'User-Agent' => 'RBFrameworks/1.0',
        ],
    ],
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
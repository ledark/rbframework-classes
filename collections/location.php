<?php 

return [
    'assets' => [
        'front/layout/assets/',
        'admin/layout/assets/',
    ],
    'placeholder' => [
        'imagem' => [
            'path' => 'front/interface/assets/images/semfoto.jpg',
        ]
    ],
    'cache' => [
        'default' => 'log/cache/',
        'images' => 'log/cache/fotos',
    ],
    'search_folders' => [
        '',
        '/',
        '../',
        '../template/',
    ],
    'search_extensions' => [
        '',
        '.php',
        '.html',
        '.css',
        '.js',
    ],
    'log_file' => __DIR__."/../../log/debug.[filename_backtrace].log",    
];
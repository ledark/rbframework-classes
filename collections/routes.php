<?php

$forbidden = function() {
    header('HTTP/1.0 403 Forbidden');
    echo '403 Forbidden';
    exit();
};

return [
    'middleware' => [
        /** Adicione rotas que se estiverem no $_SERVER['REQUEST_URI'] serão interceptadas
         * Note que o valor é parcial, ou seja, se você adicionar '/admin' ele interceptará qualquer rota que contenha '/admin' na URL
         * Use o padrão: /path/to/forbidden => callback
        */
        '/_app/' => $forbidden,
        '/.vscode' => $forbidden,
        '/.git' => $forbidden,
        '.md' => $forbidden,
        '.json' => $forbidden,
        '.lock' => $forbidden,
        '.log' => $forbidden,
        '.sql' => $forbidden,
        '.xml' => $forbidden,
        '/.htaccess' => $forbidden,
        '/composer.json' => $forbidden,
        '/phpunit.xml' => $forbidden,
        '/tests/' => $forbidden,
    ],
    'autoload' => [
        /** Adicione suas Rotas para serem carregadas automaticamente.
         * Use o padrão: path/to/routes => Namespace\
         */
        __DIR__.'/../class/Api/' => 'Api\\'
    ],
    'direct_files' => [
        /** Adicione extensões de arquivos que podem ser servidos diretamente, sem passar pelo roteamento do framework. */
        'css',
        'js',
        'json',
    ]
];
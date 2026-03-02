<?php

namespace Admin;

use RBFrameworks\BladeOne;

class Blade extends BladeOne
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function render(string $view, $injector = []): string
    {
        $injector = array_merge($injector, [
            'nomeFantasia' => 'Exemplo'
        ]);
        return parent::render($view, $injector);
    }
}
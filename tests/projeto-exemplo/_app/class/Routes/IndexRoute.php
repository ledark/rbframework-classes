<?php

namespace Routes;

use RBFrameworks\BladeOne;

class IndexRoute
{

    /**
     * @route GET /
     * @route GET /home
     * @status 200
     * @response html
     * @utf8 true
     * @descr Pagina inicial de Exemplo
     **/
    public function example(): string
    {
        return BladeOne::render('index');
    }


}
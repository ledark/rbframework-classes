<?php

namespace Route;

class ExampleRoute {
    /**
     * @route GET /example
     * @status 200
     * @response html
     * @utf8 true
     * @descr Verifica qual a página redirecionar o usuário que acessa a home
     **/
    public function example() {
        return "IT-WORKING!";
    }
}
<?php

namespace Routes;

use RBFrameworks\BladeOne;
use RBFrameworks\InputUser;

class LoginRoute
{

    /**
     * @route GET /login
     * @status 200
     * @response html
     * @utf8 true
     * @descr Pagina de Autenticação
     **/
    public function loginPage(): string
    {
        return BladeOne::render('login.index');
    }

    /**
     * @route POST /login
     * @status 200
     * @response json
     * @utf8 true
     * @descr Autenticação
     **/
    public function loginPost(): array
    {
        load_functions(['encoding', 'query']);
        $dados = database()->queryFirstRow("SELECT`cod`, `login` FROM ?_users WHERE `login` = %s AND `senha` = %s", InputUser::getFieldText('login'), InputUser::getFieldText('senha'));
        if (!is_null($dados) and !empty($dados)) {
            $_SESSION['user'] = $dados;
            return [
                'status' => 'success',
                'redirect' => (isset($_SESSION['redirect']) and !empty($_SESSION['redirect'])) ? $_SESSION['redirect'] : config('server.base_uri') . '/admin/dashboard',
            ];
        }

        return [
            'status' => 'error',
            'message' => encoding('Usuário ou senha inválidos.'),
        ];
    }

    /**
     * @route GET /logout
     * @status 200
     * @response redirect
     * @utf8 true
     * @descr Logout
     **/
    public function logoutGet(): string
    {
        session_destroy();
        return config('server.base_uri') . '/login';
    }

}
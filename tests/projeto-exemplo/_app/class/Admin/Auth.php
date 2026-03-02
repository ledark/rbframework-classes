<?php

namespace Admin;

use RBFrameworks\Core\Http;
use RBFrameworks\Core\Session;

class Auth
{

    public static function isAnybody(): void
    {
        if (!Session::get('user') and !isset(Session::get('user')['cod'])) {
            Http::redir('{httpSite}/logout');
        }
    }

}
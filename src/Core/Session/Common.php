<?php

namespace RBFrameworks\Core\Session;

use RBFrameworks\Core\Session;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Utils\Encoding;
use RBFrameworks\Core\Exceptions\DefaultException as Exception;

class Common
{
    //Get ou Setter do session_id()
    public static function sess_id($new = null):string
    {
        if (!isset($_SESSION['SESS_ID_INTERNAL'])) $_SESSION['SESS_ID_INTERNAL'] = date('YmdHis') . '_' . session_id();
        if (!is_null($new)) $_SESSION['SESS_ID_INTERNAL'] = $new;
        return $_SESSION['SESS_ID_INTERNAL'];
    }

    //Define o nome do Front para fins de separação dos dados
    public static function session_front($name = null):string
    {
        return self::session_get_or_set("SESS_FRONT_NOW", $name);
    }

    public static function session_get_or_set(string $name, $value = null):string
    {
        if (!isset($_SESSION['SESS_FRONT_NOW'])) $_SESSION['SESS_FRONT_NOW'] = 'front';
        if (!is_null($name)) $_SESSION['SESS_FRONT_NOW'] = $name;
        return $_SESSION['SESS_FRONT_NOW'];
    }    

    //Define de o Usuário está logado
    public static function session_islogged($name = 'RBAuth')
    {
        switch ($name) {
            case 'RBAuth':
                return (!isset($_SESSION['RBAuth']['data'][0]['cod'])) ? false : true;
                break;
            case 'user':
                return (!isset($_SESSION['ecommerce_users']['cod'])) ? false : true;
                break;
        }
    }

    public static function session_get(string $name = 'RBAuth', $key)
    {
        switch ($name) {
            case 'RBAuth':
                if (isset($_SESSION[$name]['data'][0][$key])) {
                    return $_SESSION[$name]['data'][0][$key];
                } else {
                    return null;
                }
                break;
        }
    }

    public static function session_logout($name = 'RBAuth', $destroy = false)
    {
        switch ($name) {
            case 'representante':
                unset($_SESSION['ecommerce_users']);
                break;
        }
        if (isset($_SESSION[$name])) unset($_SESSION[$name]);
        if ($destroy) {
            if (isset($_SESSION['SESS_ID_INTERNAL']))    unset($_SESSION['SESS_ID_INTERNAL']);
            if (isset($_SESSION['SESS_FRONT_NOW']))      unset($_SESSION['SESS_FRONT_NOW']);
            session_destroy();
        }
    }



    public static function session_die()
    {
        // Apaga todas as variáveis da sessão
        $_SESSION = array();

        // Se é preciso matar a sessão, então os cookies de sessão também devem ser apagados.
        // Nota: Isto destruirá a sessão, e não apenas os dados!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Por último, destrói a sessão
        session_destroy();
    }

    public static function session_renovate()
    {
        $session = $_SESSION;
        self::session_die();
        new Session;
        session_regenerate_id();
        $_SESSION = $session;
    }
    
    private static function getSessionPath():string {
        $path = Config::get('location.sessions_dir');
        if(!is_dir($path)) {
            Exception::throw("Não foi possível encontrar o diretório de sessões $path");
        }
        return $path;
    }

    public static function session_save(string $name = '_all', $content = null, $forceUtf8 = true)
    {
        if ($content == null) {
            $content = $_SESSION;
        }
        if (is_array($content)) {
            if ($forceUtf8) {
                Encoding::DeepEncode($content);
            }
            $content = json_encode($content);
        }
        $path = self::getSessionPath() . session_id();
        mkdir($path);
        file_put_contents("{$path}/{$name}", $content);
    }

    /**
     * Fará o include de um arquivo apenas uma vez utilizando para isso a instãncia da sessão
     *
     * @param string $filename
     * @param string $sessionName
     * @param boolean $once
     * @return void
     */
    public static function session_include(string $filename, string $sessionName, $once = false)
    {
        if (!isset($_SESSION[$sessionName])) {
            include($filename);
            if ($once) {
                $_SESSION[$sessionName] = true;
            }
        }
    }

    public static function session_include_once(string $filename, string $sessionName)
    {
        self::session_include($filename, $sessionName, true);
    }
}

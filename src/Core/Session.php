<?php
/*
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. Neither the name of copyright holders nor the names of its
   contributors may be used to endorse or promote products derived
   from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL COPYRIGHT HOLDERS OR CONTRIBUTORS
BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * @author   "Ricardo Bermejo" <ricardo@bermejo.com.br>
 * @copyright Copyright (c) 2021 Ricardo Bermejo
 * @package  Core\Session
 * @version  1.0.0 [Core v1.98.2] Ago/2021
 * @license  Revised BSD
 * @uses session_init
 * @filesource functions/session.php
 */

namespace RBFrameworks\Core;

use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Http;
use RBFrameworks\Core\Utils\Encoding;
use RBFrameworks\Core\Exceptions\DefaultException as Exception;
use GuzzleHttp\Psr7\MessageTrait;
use RBFrameworks\Core\Session\MessageTrait as SessionMessageTrait;

class Session
{

    use MessageTrait;
    use SessionMessageTrait;    

    /**
     * session_id() as default ou sobrescrito no constructor
     *
     * @var session_id
     */
    public $session_id;

    /**
     * O constructor dessa função cria uma sessão se a mesma não existir, ou atualiza-a com LAST_ACTIVITY = time()
     */
    public function __construct(?string $session_id = null)
    {
        self::session_init();
        $this->createSessionID($session_id);
    }

    private static function session_init() {
        if(!headers_sent()) {        
            if (version_compare(phpversion(), '5.4.10', '<')) {
                if(session_id() == '') {
                    session_start();
                }
            } else {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
            }
            $_SESSION['LAST_ACTIVITY'] = time();
        }
    }     

    public static function get($mixed = null) {
        $utf8_encode = null;
        if(is_bool($mixed)) {
            $utf8_encode = $mixed;
        }
        if(is_string($mixed)) {
            return isset($_SESSION[$mixed]) ? $_SESSION[$mixed] : null;
        }
        $sess = new self();
        $dados = $_SESSION;
        if($utf8_encode === true) {
            Encoding::DeepEncode($dados);
        } else
        if($utf8_encode === false) {
            Encoding::DeepDecode($dados);
        }
        return $dados;
    }

    public static function set(string $chave, $valor):void {
        $_SESSION[$chave] = $valor;
    }

    /**
     * createSessionID 
     * @return void
     */
    public function createSessionID(?string $session_id = null):void
    {
        $session_id = is_null($session_id) ? session_id() : $session_id;
        $this->session_id = $session_id === false ? uniqid('_') : $session_id;
    }

    public static function clear() {
        $_SESSION = [];
    }

}

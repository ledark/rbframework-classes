<?php 

namespace Framework;

use Framework\Input\GetterTrait;
use Framework\Input\GetFromTrait;
use Framework\Input\GetFieldTrait;
use Framework\Input\HasTrait;
use Framework\Traits\SingletonTrait;

class Input {

    private $input = [];
    private $headers = [];

    public $data = [];

    //use SingletonTrait;
    use GetterTrait;
    use HasTrait;

    use GetFromTrait;
    use GetFieldTrait;

    private static $instance = null;

    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->input = $this->phpInput();
        $this->input = !count($this->input) ? $this->phpPost() : $this->input;
        if(!count($this->input)) {
            $this->input = $this->phpGet();
        }
    }

    public static function detectFrom():string {
        $input = self::getInstance()->phpGet();            if(count($input) > 0) return 'get';
        $input = self::getInstance()->phpPost();           if(count($input) > 0) return 'post';
        $input = self::getInstance()->phpInput();          if(count($input) > 0) return 'input';
        $input = self::getInstance()->phpSession();        if(count($input) > 0) return 'session';
        $input = self::getInstance()->phpRequestHeaders(); if(count($input) > 0) return 'headers';
        return 'none';
    }

    public static function setInstance(Input $instance) {
        self::$instance = $instance;
    }


    /**
     * Retona atual array de input, que poderá ser em ordem: file://input, $_POST, $_GET
     * @param bool $forceUtf8
     * @return array
     */
    public function get(bool $forceUtf8 = null):array {
        if(is_null($forceUtf8)) return $this->input;
        if($forceUtf8) {
            $this->input = encoding($this->input);
        } else {
            $this->input = encoding_reverse($this->input);
        }
        return $this->input;
    }

    public static function getAll():array {
        return self::getInstance()->get();
    }

    public function decode():array {
        $_INPUT = [];
        foreach($this->get() as $chave => $valor) {
            $_INPUT[$chave] = is_string($valor) ? str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $valor) : $valor;
        }
        return $_INPUT;
    }

}

include_once __DIR__ . '/Input/functions.php';
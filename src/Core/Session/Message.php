<?php

namespace RBFrameworks\Core\Session;

use RBFrameworks\Core\Session;

class Message
{

    private $blockname = 'RBFv99doMSG';

    public function __construct() {
        new Session;
    }

    public function prepare():object {
        if(!$this->hasMessage()) {
            $this->setDefault();
        }
        if(!isset($_SESSION[$this->blockname]['prefix'])) $_SESSION[$this->blockname]['prefix'] = '';
        if(!isset($_SESSION[$this->blockname]['cssclass'])) $_SESSION[$this->blockname]['cssclass'] = '';
        if(!isset($_SESSION[$this->blockname]['message'])) $_SESSION[$this->blockname]['message'] = '';
        if(!isset($_SESSION[$this->blockname]['sufix'])) $_SESSION[$this->blockname]['sufix'] = '';		
        return $this;
    }

    private function setDefault(string $message = '', string $cssclass = '', $prefix = '<div class="alert alert-info">', $sufix = '</div>'):void {
        $_SESSION[$this->blockname] = [
            'prefix' => $prefix,
            'cssclass' => $cssclass,
            'message' => $message,
            'sufix' => $sufix,
            'rendered' => 0,
        ];
    }

    public function setMessage(string $message):object {
        $_SESSION[$this->blockname]['message'] = $message;
        return $this;
    }

    public function setCssClass(string $cssclass):object {
        $_SESSION[$this->blockname]['cssclass'] = $cssclass;
        return $this;
    }

    public function setPrefix(string $prefix):object {
        $_SESSION[$this->blockname]['prefix'] = $prefix;
        return $this;
    }

    public function setSufix(string $sufix):object {
        $_SESSION[$this->blockname]['sufix'] = $sufix;
        return $this;
    }

    public function clear():void {
        $_SESSION[$this->blockname] = [
            'prefix' => '',
            'cssclass' => '',
            'message' => '',
            'sufix' => '',
        ];
        //unset($_SESSION[$this->blockname]);
    }

    public function hasMessage():bool {
        if(!isset($_SESSION[$this->blockname])) return false;
        if(!isset($_SESSION[$this->blockname]['message'])) return false;
        return empty($_SESSION[$this->blockname]['message']) ? false : true;
    }

    public function render():void {
        if($this->hasMessage()) {
            echo $_SESSION[$this->blockname]['prefix'];
            echo '<span class="'.$_SESSION[$this->blockname]['cssclass'].'">'.$_SESSION[$this->blockname]['message'].'</span>';
            echo $_SESSION[$this->blockname]['sufix'];
            $this->clear();
        }
    }

}
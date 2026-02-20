<?php 

namespace Framework\Traits;

use Framework\Config;

trait SessionMessageTrait {

    private static function getBlockNameMessage():string {
        return Config::get('session.blockname', 'RBFv100doMSG');
    }

    public static function setMessage(string $message = '', string $cssclass = '', $prefix = '<div class="alert alert-info">', $sufix = '</div>'):void {        
        $_SESSION[self::getBlockNameMessage()] = [
            'prefix' => $prefix,
            'cssclass' => $cssclass,
            'message' => $message,
            'sufix' => $sufix,
            'rendered' => 0,
        ];
    }

    private static function hasMessage():bool {
        if(!isset($_SESSION[self::getBlockNameMessage()])) return false;
        if(!isset($_SESSION[self::getBlockNameMessage()]['message'])) return false;
        return empty($_SESSION[self::getBlockNameMessage()]['message']) ? false : true;
    }

    public static function setWarning(string $message):void {
        self::setMessage($message, 'alert-warning', '<div class="alert alert-warning">', '</div>');
    }

    public static function setSuccess(string $message):void {
        self::setMessage($message, 'alert-success', '<div class="alert alert-success">', '</div>');
    }

    public static function setError(string $message):void {
        self::setMessage($message, 'alert-danger', '<div class="alert alert-danger">', '</div>');
    }

    public static function render(bool $capture = false) {
        if($capture) ob_start();
        if(self::hasMessage()) {
            echo $_SESSION[self::getBlockNameMessage()]['prefix'];
            echo '<span class="'.$_SESSION[self::getBlockNameMessage()]['cssclass'].'">'.$_SESSION[self::getBlockNameMessage()]['message'].'</span>';
            echo $_SESSION[self::getBlockNameMessage()]['sufix'];
            self::clear();
        }
        if($capture) return ob_get_clean();
    }

    public static function clear() {
        $_SESSION[self::getBlockNameMessage()] = [
            'prefix' => '',
            'cssclass' => '',
            'message' => '',
            'sufix' => '',
        ];
    }

}
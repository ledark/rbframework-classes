<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Utils\Strings;

if(!function_exists('is_developer')) {
    function is_developer():bool {
        return true;
    }
}

class Debug {

    public static function isLocalhost():bool {
        if(!isset($_SERVER['REMOTE_ADDR'])) return false;
        return in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
    }

    public static function isDeveloper():bool {
        Plugin::load('helper');
        if(!function_exists('is_developer')) {
            return false;
        }
        return is_developer();
    }

    //Errors Control
    public static function displayErrors(bool $display):void {
        
        ini_set('display_errors', ($display) ? 1 : 'Off');
        ini_set('display_startup_errors', ($display) ? 1 : 'Off');
        if($display) error_reporting(E_ALL); else error_reporting(0);
        
        set_time_limit(50);
    }

    //Getter
    public static function getPrintableAsText($message):string {
        ob_start();
        if(gettype($message) == 'array') {
            print_r($message);
            $message = "";
        } else
        if(gettype($message) == 'object') {
            print_r($message);
            $message = "";
        }
        echo $message;
        return ob_get_clean();
    }

    //Getter
    public static function getPrintableAsArray($message):array {
        return ['debug' => self::getPrintableAsText($message)];
    }

    public static function devValue($mixedValue, string $title = "Core\Debug\Card"):void {
        if(is_developer()) {
            $var = new Utils\Variables($mixedValue);
            echo "<span class=\"card p-3 d-inline-block\"><span class=\"d-inline\">{$title}: </span>".$var->getStringBadged()."</span>";
        }
    }
    public static function devCard($message, string $title = "Core\Debug\Card", string $style = 'background: #D90707;color: #FFF;'):void {
        if(is_developer()) {
            ob_start();
            self::message($message);
            $message = ob_get_clean();
            echo <<<CARD
            <div class="card card-dev-danger card-danger" style="{$style}">
                <div class="card-header">
                    {$title}
                </div>
                <div class="card-body">
                    {$message}
                </div>
            </div>
CARD;
        }
    }

    public static function card($message, string $title = "Core\Debug\Card"):void {
        ob_start();
        self::message($message);
        $message = ob_get_clean();
        echo <<<CARD
        <div class="card">
            <div class="card-header">
                {$title}
            </div>
            <div class="card-body">
                {$message}
            </div>
        </div>
CARD;
    }    

    //DisplayInfo
    public static function error($message)    {
        self::message($message, 'color: red;');
    }

    //DisplayInfo
    public static function message($message, $css_class = ''):void {
        echo '<pre style="'.$css_class.'">';
        echo self::getPrintableAsText($message);
        echo '</pre>';
    }


/**
     * Nomes de GROUPS Incluídos Aqui são Excluídos do DEBUG
     *
     * @return array
     */
    private static function logIgnoreGroup():array {
        Plugin::load("helper");
        $ignored = get_config('debug.ignore_groups');
        if(!is_array($ignored)) $ignored = [];
        return array_merge($ignored, []);        
    }
    /**
     * Nomes de Arquivo Incluídos Aqui são Excluídos do DEBUG
     *
     * @return array
     */
    private static function logIgnoreFilenames():array {
        $ignored = get_config('debug.ignore_filenames');
        if(!is_array($ignored)) $ignored = [];
        return array_merge($ignored, []);
    }

    //WriteFile
    public static function log($message, array $context = [], string $group = 'log', string $filename_backtrace = null, int $backtrace_level = 0) {
        Plugin::load('utf8_encode_deep');
        $uid = uniqid();
        $message = new \Core\Utils\Variables($message);
        if(is_null($filename_backtrace)) {
            $filename_backtrace = debug_backtrace()[$backtrace_level]['file'];
            $filename_backtrace = dirname($filename_backtrace).'.'.basename($filename_backtrace, '.php');
        }
            $filename_backtrace = \Core\Utils\Strings\Dispatcher::file($filename_backtrace);
            $filename_backtrace = str_replace('\\', '-', $filename_backtrace);
            $filename_backtrace = str_replace('/', '-', $filename_backtrace);

        if(in_array($group, self::logIgnoreGroup()) == true) return;
        if(in_array($filename_backtrace, self::logIgnoreFilenames()) == true) return;


        $filename = Config::get('path.log_file');
        $filename = str_replace('[filename_backtrace]', $filename_backtrace, $filename);
        utf8_encode_deep($context);
        file_put_contents($filename, date('Y-m-d H:i:s').'['.$uid.']'.$group.': '.$message->getString().' --'.json_encode($context)."\r\n", FILE_APPEND);
    }

    public static function pre($message) {
        echo '<pre>haha';
        print_r($message);
        echo '</pre>';
    }
    
}
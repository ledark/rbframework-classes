<?php 

namespace Framework;

use Framework\Utils\Encoding;
use Framework\Types\Directory;
use Framework\Types\Variables;
use Framework\Utils\Strings\Dispatcher;

class Debug {

    public static function isLocalhost():bool {
        if(!isset($_SERVER['REMOTE_ADDR'])) return false;
        return in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
    }

    public static function isDeveloper():bool {
        if(!function_exists('is_developer')) {
            return false;
        }
        return is_developer();
    }

    public static function displayErrors(bool $display):void {

        ini_set('display_errors', ($display) ? 1 : 'Off');
        ini_set('display_startup_errors', ($display) ? 1 : 'Off');
        if($display) error_reporting(E_ALL); else error_reporting(0);

        set_time_limit(50);
    }

    public static function devCard($message, string $title = "Core\Debug\Card", string $style = 'background: #D90707;color: #FFF;'):void {
        if(self::isDeveloper()) {
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
     *
     * @return array
     */
    private static function logIgnoreGroup():array {
        //Plugin::load("helper");
        $ignored = Config::assigned('debug.ignore_groups', []);
        if(!is_array($ignored)) $ignored = [];
        return array_merge($ignored, []);
    }
    /**
     *
     * @return array
     */
    private static function logIgnoreFilenames():array {
        $ignored = Config::assigned('debug.ignore_filenames', []);
        if(!is_array($ignored)) $ignored = [];
        return array_merge($ignored, []);
    }

    public static function logFile(string $filename, string $message, array $context =[], string $group = 'log', string $directory = 'log/logs/'):void {
        global $RB_config_overwrite;
        $RB_config_overwrite['log_file'] = rtrim($directory, '/').'/'.$filename.'-[filename_backtrace].log';
        self::log($message, $context, $group, $filename);
        $RB_config_overwrite = [];
    }


    public static function log(string $message, array $context = [], string $group = 'log', string $filename = 'debug.log') {

        $skips = 0;
        $uid = uniqid();
        $message = new Variables($message);
        $filename_backtrace = date('Ymd');
        $filename_backtrace = Dispatcher::file($filename_backtrace);
        $filename_backtrace = str_replace('\\', '-', $filename_backtrace);
        $filename_backtrace = str_replace('/', '-', $filename_backtrace);

        if(in_array($group, self::logIgnoreGroup()) == true) return;
        if(in_array($filename_backtrace, self::logIgnoreFilenames()) == true) return;
        foreach(Config::assigned('debug.ignore_contexts', []) as $ignore) {
            if($ignore($context) == true) {
                $skips++;
            }
        }

        if(isset($_SERVER['REMOTE_ADDR'])) {
            if(in_array($_SERVER['REMOTE_ADDR'], Config::assigned('debug.ignore_ips', []))) return;
        }

        if($skips > 0) return;

        if(isset($_SERVER['REMOTE_ADDR'])) {
            $compl = preg_replace('/[^a-z0-9]/', '', $_SERVER['REMOTE_ADDR']).'.';
        } else {
            $compl = 'NOIP';
        }

        $filename = Config::assigned('location.log_file', 'debug.[filename_backtrace].log');
        $filename = str_replace('[filename_backtrace]', $filename_backtrace, $filename);
        $filename = str_replace('debug.', 'debug.'.$compl, $filename);
        Directory::mkdir(dirname($filename));
        Encoding::DeepEncode($context);
        file_put_contents($filename, date('Y-m-d H:i:s').'['.$uid.']'.$group.': '.$message->getString().' --'.json_encode($context)."\r\n", FILE_APPEND);
    }

    public static function pre($message) {
        echo '<pre>';
        print_r($message);
        echo '</pre>';
    }

    public static function getFileBacktrace():array {
        $res = [];
        $backtrace = debug_backtrace();
        foreach($backtrace as $level => $prop) {
            $res[$level] = '';
            if(isset($prop['file']) and isset($prop['line'])) {
                $res[$level] = $prop['file'].':'.$prop['line'];
            } else {
                if(isset($prop['function']) and is_string($prop['function']) ) $res[$level].= 'fn:'.$prop['function'];
                if(isset($prop['class']) and is_string($prop['function'])) $res[$level].= ' ['.$prop['class'].']';
            }
            if(empty($res[$level])) unset($res[$level]);
        }
        return $res;
    }

    public static function getFilenameBacktrace(int $level, bool $withLine = false):string {
        $backtrace = self::getFileBacktrace();
        if($level > count($backtrace)) $level = count($backtrace);
        if(!isset($backtrace[$level])) return '';
        $filename = $backtrace[$level];
        if(!$withLine) {
            $filename = explode(':', $filename);
            $filename = $filename[0];
            return $filename;
        } else {
            return $filename;
        }

    }


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

    public static function getPrintableAsArray($message):array {
        return ['debug' => self::getPrintableAsText($message)];
    }

    public static function preDanger():void {
        $args = func_get_args();
        echo '<pre style="display: block;position: absolute; z-index:9999; top: 0; left:0; background: rgba(155, 0, 0, 0.9);margin: 0;padding: 2em;color: #FFF;">';
        echo '<strong style="background: #f9bf52;display: block;color: #b52e2e;font-size: 110%;padding: 1em;">'.self::getPrintableAsText($args[0]).'</strong>';
        foreach($args as $i => $arg) {
            if($i == 0) continue;
            print_r($arg);
        }
        echo '</pre>';
    }

}

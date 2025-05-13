<?php 

namespace Framework;

class Debug {
    public static function log(string $message, array $context = [], string $group = 'log', string $filename = 'debug.log') {

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

}
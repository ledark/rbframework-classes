<?php

namespace RBFrameworks;

class Exception extends \Exception {
    
    public function __construct($message, $code=NULL)    {
        parent::__construct($message, $code);
    }
   
    public function __toString()    {
        return "Code: " . $this->getCode() . "<br />Message: " . htmlentities($this->getMessage());
    }
   
    public function getException()    {
        print $this; // This will print the return from the above method __toString()
    }
    
    public static function logErrorBlock(array $info):void {
        $uid = session_id();
        $text = date('Y-m-d H:i:s').'[';
        $text.= $_SERVER['REMOTE_ADDR'];
        $text.= ']';
        $text.= $info['message'];
        $text.= ':';
        $text.= $info['errorno'];
        $text.= ' --> ';
        $text.= $info['file'];
        $text.= ':';
        $text.= $info['line'];
        $text.= ' ('.$uid.')';
        $text.= "\r\n";
        $details = "($uid) ";
        $details.= strip_tags(self::printErrorBlock($info));
        $details.= "\r\n----------------\r\n";
        file_put_contents('log/error/Exception', $text, FILE_APPEND);
        file_put_contents('log/error/ExceptionDetail', $details, FILE_APPEND);
    }
    
    public static function printErrorBlock(array $info):string {
        $info['uid'] = uniqid('div');
        ob_start();
        print_r($info['tracearray']);
        $info['tracearray'] = ob_get_clean();
        ob_start();
        ?>
        <div style="display: block;border: 2px solid red;margin: 0;padding: 1em;background: #FFFCE0;font-family: monospace;border-left: 1em solid red;">
            <span style="color: red;font-size: 120%;">{message}</span>
            <span style="color: red;font-weight: bold;background: #ffefc8;">{errorno}</span>
            <span style="">{file}</span>
            <span style="position: relative;left: -5px;font-size: 110%;">:{line}</span><br/>
            <span style="">{previous}</span>
            <span style="cursor:pointer; padding: 5px;background: #fbedcd;line-height: 2em;border-radius: 1em;" onclick="document.getElementById('{uid}').style.display = ''">{tracestring}</span>
            <div id="{uid}" style="display: none;"><pre style="border-left: 1em solid #CCC;padding: 1em;background: #eee;">{tracearray}</pre></div>
        </div>            
        <?php
        return smart_replace(ob_get_clean(), $info, true);
    }
    
    private static function getExceptionInfo($exception):array {
        
        $previous = $exception->getPrevious();
        if(!is_null($previous)) {
            ob_start();
            print_r($previous);
            $previous = ob_get_clean();
        }
        
        return [
            'message' => $exception->getMessage(),
            'errorno' => (string) $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => (string) $exception->getLine(),
            'previous' => $previous,
            'tracestring' => $exception->getTraceAsString(),
            'tracearray' => $exception->getTrace(),
        ];
    }
   
    /**
     * handler das Excepetions faz com que as mensagens caiam aqui
     * @param type $exception
     */
    public static function getStaticException($exception) {
        if(is_developer()) {
           echo self::printErrorBlock(self::getExceptionInfo($exception));
        } else {
           self::logErrorBlock(self::getExceptionInfo($exception));
        }
    }
    
    public static function getStaticError(int $errno , string $errstr) {
        $uid = uniqid('div');
        if(is_developer()) {
            echo '<div style="display: block;border: 2px solid red;margin: 0;padding: 1em;background: #FFFCE0;font-family: monospace;border-left: 1em solid red;">';
            echo <<<TES
            <span style="color: red;font-size: 120%;">{$errstr}</span>
            <span style="color: red;font-weight: bold;background: #ffefc8;">{$errno}</span>
            <span style="cursor:pointer; padding: 5px;background: #fbedcd;line-height: 2em;border-radius: 1em;" onclick="document.getElementById('{$uid}').style.display = ''">...+</span>
            
TES;
            echo "<div id=\"{$uid}\" style=\"display: none;\">";
            echo '<pre style="border-left: 1em solid #CCC;padding: 1em;background: #eee;">';
            print_r(debug_backtrace());
            echo '</pre>';
            echo '</div>';
            echo '</div>';
        } else {
             file_put_contents('log/error/Shutdown', date('Y-m-d H:i:s')."| ".serialize($error)."\r\n", FILE_APPEND);
        }
    }
    
    public static function getStaticShutdown() {
        $error = error_get_last();
        if($error != null) {
            if(is_developer()) {
                echo '<div style="display: block;border: 2px solid red;margin: 0;padding: 1em;background: #FFFCE0;font-family: monospace;border-left: 1em solid red;">';
                echo '<pre style="border-left: 1em solid #CCC;padding: 1em;background: #eee;">';
                var_dump($error);
                echo '</pre>';
                echo '</div>';
            } else {
                 file_put_contents('log/error/Shutdown', date('Y-m-d H:i:s')."| ".serialize($error)."\r\n", FILE_APPEND);
            }
        }
    }
    
}
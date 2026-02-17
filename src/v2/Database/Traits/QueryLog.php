<?php 

namespace RBFrameworks\Core\Database\Traits;

use RBFrameworks\Core\Debug;

trait QueryLog {    

    //Name
    public function setName($name):object {
        $this->name = $name;
        return $this;
    }    

    public function writeLog($message):void {
        
        //Definição do Nome do Arquivo
        $id = $this->name;
        $filename = $this->logfolder.'QueryLogs_'. $id.'.sql';
        
        //Tratamento com a Mensagem do Log
        $message = str_replace("\r\n", " ", $message);
        $message = str_replace("\t", " ", $message);
        $message = str_replace("  ", " ", $message);
        $message = str_replace("  ", " ", $message);
        
        if(!isset($_SERVER['REMOTE_ADDR'])) $_SERVER['REMOTE_ADDR'] = 'localhost';
        $prefix = "/* ".date("Y-m-d H:i:s")." [".$_SERVER['REMOTE_ADDR']."] in ".__FILE__." */ \n";

       // file_put_contents($filename, $prefix.$message."\r\n\r\n", FILE_APPEND);
        Debug::log($message, [], $this->name);
        
    }    

}
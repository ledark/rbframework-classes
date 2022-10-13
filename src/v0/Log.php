<?php

namespace RBFrameworks;

class Log {
    
    public $folder = 'log/';
    public $name = 'no_named';
    public $contents = [];
    public $append_separator = "|";
    
    public function __construct(string $name) {
        $this->name = $name;
        if(!is_dir($this->folder)) throw new \Exception("Diretório inválido");
    }
    
    public function setFolder(string $path) {
        $this->folder = $path;
         if(!is_dir($this->folder)) throw new \Exception("Diretório inválido");
        return $this;
    }
    
    public function setGroup(string $name) {
        $this->group = $name;
        return $this;
    }
    
    public function append($mixed) {
        $this->contents[] = $mixed;
        return $this;
    }
    
    private function logHeader():string {
        return date("Y-m-d H:i:s").'['.$this->group.']';
    }
    
    private function logFooter():string {
        return "\r\n";
    }
    
    private function getContents():string {
        $res = $this->logHeader();
        foreach($this->contents as $content) {
            if(is_string($content)) {
                $res.= $content.$this->append_separator;
            }
            //@todo is_array is_object
        }
        return $res.$this->logFooter();
    }
    
    public function write() {
        if(!file_exists($this->folder.$this->name)) {
            touch($this->folder.$this->name);
        }
        file_put_contents($this->folder.$this->name, $this->getContents());
    }
    
}

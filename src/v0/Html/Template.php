<?php

namespace RBFrameworks\Html;

class Template {
    
    public static $tmpldir = __DIR__.'/Templates/';
    public $content = '';
    private $ext = ['.html', '.htm', '.tmpl', '.php'];
    
    public function __construct(string $name) {
        $this
            ->searchContent($name)
        ;
    }
    
    public function searchContent(string $name) {
        foreach($this->ext as $extension) {
            $filename = self::$tmpldir.$name.$extension;
            if(file_exists($filename)) {
                $this->addContent(file_get_contents($filename));
                return $this;
            }
        }
        return $this;
    }
    
    public function __toString() {
        return $this->content;
    }
    
    public function addContent(string $content){
        $this->content = $content;
    }
    
}

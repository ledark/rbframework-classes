<?php

namespace RBFrameworks\Core\Utils;

use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Utils\Replace;

class FileReplace
{

    public $file;
    public $replace;

    public function __construct(string $filename, array $replaces = []) {
    
        //DefineFile
        $this->file = new File($filename);
        $this->file->addSearchFolder(__DIR__.'/../Templates/');
        $this->file->addSearchExtension('.tmpl');

        //UseReplaces
        $this->replace = new Replace($this->file->getFileContents(), $replaces);
        
    }

    public function addSearchFolder(string $folder) {
        $this->file->addSearchFolder($folder);
        return $this;
    }

    public function addSearchExtension(string $extension) {
        $this->file->addSearchExtension($extension);
        return $this;
    }

    public function inputEncoding(string $encoding = '') {
        return $this;
    }

    public static function get(string $filename, array $replaces = []):string {
        return (new self($filename, $replaces))->render(true);
    }
    public static function display(string $filename, array $replaces = []) {
        echo self::get($filename, $replaces);
    }

    public function render(bool $capture = false) {
        if($capture) return $this->replace->render($capture); else $this->replace->render($capture);
    }

    public function __toString():string {
        return $this->render(true);
    }
}

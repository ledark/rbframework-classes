<?php

namespace RBFrameworks\Core;

class Directory {
    
    private $path;
    private $variables = [];

    public function __construct(string $path) {
        $this->path = $path;
        return $this;
    }

    public function isDir() {
        return true;
    }
    public function isValidDir() {
        return is_dir($this->path);
    }

    public function getDirectory():string {
        return $this->path;
    }

    public function addReference(string $referenceName, &$value) {
        $this->variables[$referenceName] = $value;
    }

    public function addVariable(string $variableName, $value) {
        $this->variables[$variableName] = $value;
        return $this;
    }

    public function getVariables():array {
        return $this->variables;
    }

    public function includeAll():void {
        extract($this->getVariables());
        foreach (new \DirectoryIterator($this->getDirectory()) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($fileInfo->isDir()) continue;
            include($fileInfo->getPathname());
        }        
    }

    public function getList():array {
        $list = [];
        foreach (new \DirectoryIterator($this->getDirectory()) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $list[] = $fileInfo->getPathname();
        }
        return $list;
    }

    public function getRecursiveFiles(string $path = null):array {
        if(is_null($path)) $path = $this->getDirectory();
        $list = [];
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($fileInfo->isDir()) $list[] = $this->getRecursiveFiles($fileInfo->getBasename());
            $list[] = $fileInfo->getPathname();
        }
        sort($list);
        return $list;
    }

}
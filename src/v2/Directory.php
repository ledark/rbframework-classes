<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Types\Directory as DirectoryType;

class Directory {
    
    private $path;
    private $variables = [];

    public function __construct(string $path) {
        $this->path = $path;
        return $this;
    }

    public static function mkdir(string $path, int $mode = 0755, bool $recursive = true):void {
		$path = str_replace('\\', '/', $path);
		$path = ltrim($path, '/');
        if(!is_dir($path)) {        
            $parts = explode('/', $path);
            foreach($parts as $key => $part) {
                if($part == '') continue;
                $path = implode('/', array_slice($parts, 0, $key+1));
                if(!is_dir($path)) {
                    mkdir($path, $mode, $recursive);
                }
            }
        }

        if(!is_dir($path)) {
            throw new \Exception("Directory {$path} not exists");
        }
    }

    public static function rmdir(string $path, bool $deltree = false):void {
        if(is_dir($path)) {
            $parts = explode('/', $path);
            foreach($parts as $key => $part) {
                if($part == '') continue;
                $path = implode('/', array_slice($parts, 0, $key+1));
                if(is_dir($path)) {
                    self::deltree($path, $deltree);
                }
            }
        }
    }

    private static function deltree(string $src, bool $deltree = true) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    self::deltree($full);
                }
                else {
                    return;
                    if($deltree) unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }

    
    public static function existsDirectory(string $directoryPath):bool {
        return DirectoryType::existsDirectory($directoryPath);
    }
    public static function needsDirectory(string $directoryPath) {
        return DirectoryType::needsDirectory($directoryPath);
    }
    public static function trimPath(string $path):string {
        return DirectoryType::trimPath($path);
    }
    public static function rtrim(string $path):string {
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        return $path;
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

    /**
     * getListFromDirectory function
     *
     * @param string $filename
     * @param callable|null $callback
     * @return array
     */
    public static function getListFrom(string $filename, callable $callback = null):array {
        $list = [];
        if(!is_dir($filename)) {
            $list[] = $filename;
            return $list;
        }        
        foreach (new \DirectoryIterator($filename) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($callback) {
                $callback($fileInfo);
            }
            $list[] = $fileInfo->getPathname();
        }
        return $list;
    }

    public function getRecursiveFiles(string $path = null, callable $callback = null):array {
        if(is_null($path)) $path = $this->getDirectory();
        $list = [];
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($fileInfo->isDir()) $list[] = $this->getRecursiveFiles($fileInfo->getBasename());
            if($callback) {
                $callback($fileInfo);
            }            
            $list[] = $fileInfo->getPathname();
        }
        sort($list);
        return $list;
    }

}
<?php

namespace RBFrameworks\Core\Types;

class Directory {
    
    private $path;
    private $originalpath;
    private $variables = [];

    public function __construct(string $path) {
        $this->originalpath = $path;
        $this->path = self::trimPath($path);
        return $this;
    }

    public static function trimPath(string $path):string {
        $path = trim($path);
        $path = trim($path, '/');
        $path = trim($path, '\\');
        return $path;
    }

    public function isDir() {
        return true;
    }
    public function isValidDir():bool {
        return is_dir($this->path);
    }

    public function getDirectory():string {
        return empty($this->path) ? './' : $this->path;
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

    /**
     * includeAll use para incluir todos os arquivos em Core\Directory
     * Se houverem variáveis injectas através de $myDirectory->addVariable('key', 'value') ou $myDirectory->addReference('key', $myAnotherVar)
     *
     * @return void
     */
    public function includeAll() {
        extract($this->getVariables());
        foreach (new \DirectoryIterator($this->getDirectory()) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            include($fileInfo->getPathname());
        }        
    }

    /**
     * getList function [Não Recursivo]
     * 
     * @param string|null $path pode ser qualquer directório, ou null para usar o atual definido em Core\Directory
     * @param callable|null $callbackBefore($fileInfo) passe uma função que retorna TRUE para incluir o arquivo ou false para ignorar o arquivo
     * @return array
     */
    public function getList(string $path = null, callable $callbackBefore = null):array {
        if(is_null($callbackBefore)) $callbackBefore = function():bool { return true; };
        if(is_null($path)) $path = $this->getDirectory();        
        $list = [];
        foreach (new \DirectoryIterator($this->getDirectory($path)) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($callbackBefore($fileInfo)) $list[] = $fileInfo->getPathname();
        }
        return $list;
    }

    /**
     * getRecursiveFiles function [Recursivo]
     *
     * @param string|null $path pode ser qualquer directório, ou null para usar o atual definido em Core\Directory
     * @param callable|null $callbackBefore($fileInfo) passe uma função que retorna TRUE para incluir o arquivo ou false para ignorar o arquivo
     * @return array
     */
    public function getRecursiveFiles(string $path = null, callable $callbackBefore = null):array {
        if(is_null($callbackBefore)) $callbackBefore = function():bool { return true; };
        if(is_null($path)) $path = $this->getDirectory();
        $list = [];
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($fileInfo->isDir() and $callbackBefore($fileInfo)) $list[] = $this->getRecursiveFiles($fileInfo->getPathname(), $callbackBefore);
            if($callbackBefore($fileInfo)) $list[] = $fileInfo->getPathname();
        }
        sort($list);
        return $list;
    }  

    /**
     * static needsDirectory retorna o objeto Directory, ou falha se não conseguir.
     *
     * @param string $directoryPath
     * @param string|null $message
     * @return Directory
     */
    public static function needsDirectory(string $directoryPath, string $message = null): Directory {
        $message = is_null($message) ? "Directory {$directoryPath} not found" : $message;
        $directoryObject = new self($directoryPath);
        if(!$directoryObject->isValidDir()) throw new \Exception($message);
        return $directoryObject;
    }

    public static function existsDirectory(string $directoryPath):bool {
        return (new self($directoryPath))->isValidDir();
    }

    public function getDirectoryWithoutEndSlash(): string {
        $path = $this->getDirectory();
        $path = self::trimPath($path);
        return $path;
    }

    public function getDirectoryWithEndSlash(): string {
        $path = $this->getDirectory();
        $path = self::trimPath($path);
        return $path.DIRECTORY_SEPARATOR;
    }
}
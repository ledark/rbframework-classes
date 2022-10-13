<?php

namespace RBFrameworks\Database;

trait DaoQuery {
    
    public $registredQuerys = [];
    
    public function isRegistredQuery(string $name):bool {
        return isset($this->registredQuerys[$name]);
    }
    
    public function registerQuery(string $name, string $query, bool $save = false) {
        $this->registredQuerys[$name] = $query;
        if($save) $this->saveRegistredQuery($name, $query);
    }
    
    public function applyVariables(string $string) {
        return smart_replace($string);
    }
    
    public function getRegistredQuery(string $name) {
        if(file_exists($this->getRegistredFile($name))) {
            $this->registerQuery($name, $this->applyVariables(file_get_contents($this->getRegistredFile($name))), false);
        }
        return (isset($this->registredQuerys[$name])) ? $this->registredQuerys[$name] : $this->Query->render();
    }
    
    private function getRegistredFolder():string {
        return __DIR__."/Querys";
    }
    
    private function getRegistredFile(string $name):string {
        return $this->searchRegistredFile($name);
        return $this->getRegistredFolder()."/$name.sql";
    }
    
    private function searchRegistredFile(string $name) {
        $patterns = [];
        $patterns[] = $this->getRegistredFolder()."/$name.sql";
        
        $backtrace = debug_backtrace();
        foreach($backtrace as $trace) {
            if(isset($trace['file'])) {
                $patterns[] = dirname($trace['file'])."/$name.sql";
            }
        }
        
        foreach($patterns as $file) {
            if(file_exists($file)) {
                return $file;
            }
        }
        
        return $this->getRegistredFolder()."/$name.sql";
        
    }
    
    public function saveRegistredQuery($name, $query) {
        if(!file_exists($this->getRegistredFile($name))) {
            file_put_contents($this->getRegistredFile($name), $query);
        } else
        if(!file_exists($this->getRegistredFile($name).'~compiled')) {
            file_put_contents($this->getRegistredFile($name).'~compiled', $query);
        }
        
    }
    
    public function saveAllRegistredQuerys() {
        foreach($this->registredQuerys as $name => $query) {
            $this->saveRegistredQuery($name, $query);
        }
    }
    
    public function clearAllRegistredQuerys() {
        $now = time();
        $ttl = 60*60*24;
        foreach (new \DirectoryIterator($this->getRegistredFolder()) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($fileInfo->getExtension() == 'bak' and ($now-$fileInfo->getCTime()) > $ttl ) {
                unlink($fileInfo->getPathname());
            } else {
                rename($fileInfo->getPathname(), $fileInfo->getPath()."/".$fileInfo->getFilename().date('Y-m-d-His').'.bak');
            }
        }
    } 
    
}

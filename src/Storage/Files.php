<?php

namespace RBFrameworks\Storage;

class Files {
    
    const tmpFolder = 'log/cache';
    
    public $globals = [];
       
    /**
     * Você passa quantos argumentos desejar, 
     * e eles serão tratados como globais para todos os includes desta classe.
     * @return $this
     */
    public function setVars(array $variables): object {
        foreach($variables as $name => $value) {
            $this->globals[$name] = $value;
        }
        return $this;
    }
    
    /**
     * Essa função simplesmente faz um include de tudo o que estiver em um diretório.
     * @param string $directory diretório válido
     * @return void apenas um include de tudo
     * @throws \Exception
     */
    public function include_all(string $directory):void {
        
        if(!is_dir($directory)) throw new \Exception("Diretorio {$directory} informado invalido");

        foreach (new \DirectoryIterator($directory) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($fileInfo->isDir()) { 
                $this->include_all($fileInfo->getPathname());
                continue;
            }
            
            //Arquivo Válido
            $path = $fileInfo->getPath();
            $name = $fileInfo->getFilename();
            $fullpath = $path.'/'.$name;
            
            $dateCreate = date('d/m/Y', $fileInfo->getCTime());
            $dateModify = date('d/m/Y', $fileInfo->getMTime());
            $dateAccess = date('d/m/Y', $fileInfo->getATime());
            
            $this->include($fullpath);
            
        }
        
        
    }
    
    public function include(string $filepath):void {
        extract($this->globals);
        if(file_exists($filepath)) {
            include($filepath);
        } else {
            throw new \Exception("Arquivo {$filepath} não pode ser incluído");
        }
    }
    
    public function include_replace(string $filepath, array $replaces) {
        ob_start();
        $this->include($filepath);
        $contents = ob_get_clean();
        echo smart_replace($contents, $replaces, true);
    }
    
    public function getSafePath(string $realpath) {
        $fakepath = '';
        if(!is_dir($realpath) and file_exists($realpath)) {
            $fakepath = 'log/cache/'.md5(dirname($realpath)).'__'.basename($realpath);
            if(!file_exists($fakepath)) {
                copy($realpath, $fakepath);
            }
        }
        return $fakepath;
    }
    
}

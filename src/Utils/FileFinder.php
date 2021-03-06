<?php

namespace RBFrameworks\Utils;

class FileFinder {
    
    private $search_name;
    private $search_folders = [
        '',
        '_app/collections/',
        '_app/collections/database',
    ];
    private $search_extensions = [
        '',
        '.htm',
        '.html',
        '.tmpl',
        '.php',
    ];
    
    private $filename;
    private $has_founded = false;
    private $debug = false;


    public function __construct(string $search_name) {
        $this->search_name = $search_name;
        $this->generateSerchFolders();
    }

    public function debug():object {
        $this->debug = true;
        return $this;
    }
    
    public function clearSearchFolders() {
        $this->search_folders = [];
        return $this;
    }
    
    public function clearSearchExtensions() {
        $this->search_extensions = [];
        return $this;
    }
    
    public function addSearchFolder(string $path) {
        $this->search_folders[] = $path;
        return $this;
    }
    
    /**
     * Adiciona uma extens�o para realizar a busca que precisa do ponto.
     * Exemplo: $myFileFinder->addSearchExtension('.vue')->addSearchExtension('.js');
     * @param string $extension
     * @return $this
     */
    public function addSearchExtension(string $extension) {
        $this->search_extensions[] = $extension;
        return $this;
    }
    
    public function search() {
        $this->has_founded = $this->searchFilename();
        return $this;
    }
    
    public function getFilename():string {
        return $this->exists() ? $this->filename : $this->search_name;
    }
    
    public function getContents():string {
        return file_get_contents($this->getFilename());
    }
    
    public function includeContents() {
        return include($this->getFilename());
    }
    
    public function exists():bool {
        return $this->has_founded;
    }

    private function generateSerchFolders(): void {
        $tree = [];
        $backtrace = debug_backtrace();
        if(is_array($backtrace)) {
            foreach($backtrace as $trace) {
                if(isset($trace['file'])) {
                    $tree[] = dirname($trace['file']).'/';
                }
            }
        }
        $this->search_folders = array_merge($this->search_folders, $tree);
    }
    
    private function searchFilename(): bool {
        foreach($this->search_folders as $folder) {
            foreach($this->search_extensions as $extension) { 
                if($this->debug) echo '<pre>'.$folder.$this->search_name.$extension;                   
                if(file_exists($folder.$this->search_name.$extension)) {
                    $this->filename = $folder.$this->search_name.$extension;
                    if($this->debug) echo ' [FOUNDED]';
                    return true;
                }
                if($this->debug) echo '</pre>';
            }
        }
        return false;
    }
    
}

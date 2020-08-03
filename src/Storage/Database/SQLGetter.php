<?php

namespace RBFrameworks\Storage\Database;

use RBFrameworks\Request\Request as req;
use RBFrameworks\Storage\Database as DatabaseConnection;
use RBFrameworks\Storage\Database\Model as Model;
use RBFrameworks\Utils\FileFinder as FileFinder;

class SQLGetter {
    
    use Config;
    
    private $name, $originalQuery;
    private $finalQuery = "";
    private $replaces = [];
    
    public function __construct(string $name, array $replaces = []) {
        $this->setName($name);
        $this->setReplaces($replaces);
    }
    
    public function setName(string $name) {
        if(self::isSQL($name)) {
            $this->name = 'sql_'.md5($name);
            $this->setOriginalQuery($name);
        } else {
            $this->name = $name;
            
            $contents = (new FileFinder($name))
                ->clearSearchExtensions()
                ->addSearchExtension('.sql')
                ->addSearchFolder(__DIR__.'/Querys/')
                ->search()
                ->getContents()
            ;
            
            $this->setOriginalQuery($contents);
        }
    }
    
    public function setReplaces(array $replaces) {
        
        $databaseVariables = $this->getConfig('doDBv4');

        $this->replaces = array_merge([
            'httpSite' => (new req())->getHttpSite(),
            'database' => $databaseVariables['database'],
            'prefixo' => $databaseVariables['prefixo'],
        ], $this->replaces, $replaces);
    }
    
    private function applyReplaces() {
        $replaces = $this->replaces;
        $this->setFinalQuery(preg_replace_callback('/{([^}]*)}/m', function($matches) use ($replaces) {
            return (isset($replaces[$matches[1]])) ? $replaces[$matches[1]] : $matches[0];
        }, $this->getOriginalQuery()));
    }
    
    public function setFinalQuery(string $query) {
        $this->finalQuery = $query;
    }
    
    private function getOriginalQuery() {
        return $this->originalQuery;
    }
    
    private function setOriginalQuery(string $query) {
        $this->originalQuery = $query;
    }
    
    public function getFinalQuery() {
        $this->applyReplaces();
        return empty($this->finalQuery) ? '' : $this->finalQuery;
    }
    
    public static function extractTables(string $query):array {
        $re = '/(from|join)([\s]|[\n])+[`"\']?([a-zA-Z0-9_]+)/ixD';
        preg_match_all($re, $query, $matches);
        return isset($matches[3]) ? $matches[3] : [];
    }
    
    public function fetchSQL():array {
        $database = new DatabaseConnection();
        $query = $this->getFinalQuery();
        $tables = self::extractTables($query);
        $prefixo = $this->getConfig('doDBv4')['prefixo'];
        $dados = $database->setQuery($query)->execute()->getArray();
        
        //FindModel
        $collection = new FileFinder(substr($tables[0], strlen($prefixo)));
        $collection
            ->addSearchFolder('_app/collections/database/')
            ->addSearchFolder("_app/collections/database/{$prefixo}")
            ->search()
        ;
        
        if($collection->exists()) {
            $model = new Model($collection->includeContents());
            $dados = $model->humanize($dados);
        }
        return $dados;        
        
        
        return [];
    }
    
    public static function isSQL(string $query): bool {
        $query = strtoupper($query);
        $query = trim($query);
        $query = substr($query, 0, 6);
        if(
            strpos($query, 'SELECT') !== false ||
            strpos($query, 'INSERT') !== false ||
            strpos($query, 'UPDATE') !== false ||
            strpos($query, 'DELETE') !== false 
        ) {
            return true;
        }
        return false;

    }
    
}

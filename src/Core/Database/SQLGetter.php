<?php

namespace RBFrameworks\Core\Database;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Http;


class SQLGetter {

    private $name, $originalQuery;
    private $finalQuery = "";
    private $replaces = [];

    public static function query(string $query, array $replaces = []) {
        $query = new self($query, $replaces);
        return $query->getFinalQuery();
    }

    public function __construct(string $name, array $replaces = []) {
        \RBFrameworks\Core\Debug::log($name, $replaces, "SQLGetter", "CoreDatabase");
        $this->setReplaces($replaces);
        $this->setName($name);
    }

    public function setReplaces(array $replaces) {
        
        $databaseVariables = Config::get('database');

        $this->replaces = array_merge([
            'httpSite' => Http::getSite(),
            'database' => $databaseVariables['database'],
            'prefixo' => $databaseVariables['prefixo'],
        ], $this->replaces, $replaces);
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

    public function setFinalQuery(string $query) {
        $this->finalQuery = $query;
    }
    
    private function getOriginalQuery() {
        return $this->originalQuery;
    }
    
    private function setOriginalQuery(string $query) {
        $this->originalQuery = $query;
    }

    private function applyReplaces() {
        $replaces = $this->replaces;
        $this->setFinalQuery(preg_replace_callback('/{([^}]*)}/m', function($matches) use ($replaces) {
            return (isset($replaces[$matches[1]])) ? $replaces[$matches[1]] : $matches[0];
        }, $this->getOriginalQuery()));
    }    
    
    public function getFinalQuery() {
        $this->applyReplaces();
        return empty($this->finalQuery) ? '' : $this->finalQuery;
    }

    public function setName(string $name) {
        if(self::isSQL($name)) {
            $this->name = 'sql_'.md5($name);
            $this->setOriginalQuery($name);
        } else {
            $this->name = $name;
            
            $file = new \RBFrameworks\Core\Types\File($name);

            $file                
            ->addSearchExtension('.sql')
            ->addSearchFolder( dirname(debug_backtrace()[0]['file']).'/' )
            ->addSearchFolder( dirname(debug_backtrace()[1]['file']).'/' )
            ->addSearchFolder( dirname(debug_backtrace()[0]['file']).'/../' )
            ->addSearchFolder( dirname(debug_backtrace()[1]['file']).'/../' )

            ->addSearchFolder( dirname(debug_backtrace()[0]['file']).'/Sql/' )
            ->addSearchFolder( dirname(debug_backtrace()[1]['file']).'/Sql/' )
            ->addSearchFolder( dirname(debug_backtrace()[0]['file']).'/../Sql/' )
            ->addSearchFolder( dirname(debug_backtrace()[1]['file']).'/../Sql/' )
            ->addSearchFolder(__DIR__.'/Querys/');

            if(!$file->hasFile()) throw new \Exception("SQLGetter Failed to Read Query");

            $contents = $file->getFileContents();
            $replacement = new \RBFrameworks\Core\Utils\Replace($contents, $this->replaces);

            $contents = $replacement->render(true);
                        
            $this->setOriginalQuery($contents);
        }
    }
    
    public function fetchSQL():array {
        return [];
    }

}
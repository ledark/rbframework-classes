<?php

namespace RBFrameworks\Helpers;

use RBFrameworks\Helpers\Php\Arrays as ArrayUtils;
use RBFrameworks\Helpers\Collections\HandleCollections as Handler;

class Collections {
    
    /**
     * Localiza��o de uma pasta que contenta os arquivos PHP que retornam arrays estruturas como Collections
     */
    const collections_folder = '_app/collections';
    
    public $collection_name = '';
    public $collection_file = '';
    public $collection = [];
    public $value = null;
    public $handler = null;
    
    
    public function __construct(string $collection, string $varname = '', string $handler = 'private') {
        
        $this->collection_name = $collection;
        
        try { 
            $this->checkDir();
            $this->checkFiles($collection);
            $this->handler = new Handler($handler);
            $this->collection = $this->handler->init($this);

            $this->attemptInclude();
            
        } catch(Exception $e) {
            \Helpers\FileSystem::writeLog($e->getMessage());
            return false;
        }
        
        if(strpos($collection, '.') !== false) {
            $collection = explode('.', $collection);
            array_shift($collection);
            $collection = implode('.', $collection);
            $this->value = $this->get($collection);
            if(is_array($this->value)) {
                $this->collection = $this->value;
            } else {
                return $this->value;
            }
        }

        if(isset($this->collection[$varname]['onRequest'])) {
            if(is_callable($this->collection[$varname]['onRequest'])) $this->collection[$varname]['onRequest']();
        }
        
        return $this;
    }
      
    /**
     * isChainable
     * Reduz a colletion recuperada para a varname.
     * Pode ser utilizada mais vezes para m�ltimos filters, por�m, ela sobrescrever� o valor da collection atual.
     * @param string $varname
     */
    public function filter(string $varname): object {
        if(isset($this->collection[$varname])) {
            $this->collection = $this->collection[$varname];
        }
        return $this;
    }
    
    /**
     * Chame $myCollection->get("item.subitem.another"); para trazer um valor da cole��o
     * @param string $varname que pode ser com "dot notation"
     * @param mixed $defaultValue opcional para usar quando n�o h� valor definido. Padr�o � null
     * @return mixed
     */
    public function get(string $varname, $defaultValue = null) {
        $this->collection = $this->getCollection();
        if(isset($this->collection[$varname])) return $this->collection[$varname];
        if(strpos($varname, '.') !== false) return ArrayUtils::getValueByKey($varname, $this->collection, $defaultValue);
    }

    protected function var_export($expression, $return=false) {
        $export = var_export($expression, TRUE);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
            "/\d => /" => '',
        ];
        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
        if ((bool)$return) return $export; else echo $export;
    }

    /**
     * Grava os dados atuais de $this->collection de acordo com o $this->handler existente
     * @return void
     */
    public function set(string $varname, $newvalue) {

        $this->collection = $this->getCollection();
        
        if(isset($this->collection[$varname])) {
            $this->collection[$varname] = $newvalue;
        }
        
        if(strpos($varname, '.') !== false) {
            ArrayUtils::setValueByKey($varname, $this->collection, $newvalue);
        }
        
        $this->handler->save($this);
    }
    
    private function checkDir(): void {
        if(!is_dir(self::collections_folder)) {
            throw new Exception("Diret�rio necess�rio ".self::collections_folder." para as collections n�o encontrado");
        }
    }

    private function checkFiles(string $collection): void {
        
        $file2include = self::collections_folder."/{$collection}.php";
        $file2include = (!file_exists($file2include)) ? self::dotnotation2paths($collection) : $file2include;
        $file2include = (!file_exists($file2include)) ? self::dotnotation2path($collection) : $file2include;
        
        if(file_exists($file2include)) {
            $this->collection_file = $file2include;
        } else {
            throw new Exception("Arquivo de cole��o necess�rio $file2include n�o encontrado");
        }
    }
    
    private static function dotnotation2paths(string $collection): string {
        if(strpos($collection, '.') !== false) {
            $collection = explode('.', $collection);
            $collection = implode('/', $collection);
            return self::collections_folder."/{$collection}.php";
        }
        return '';
    }
    private static function dotnotation2path(string $collection): string {
        if(strpos($collection, '.') !== false) {
            $collection = explode('.', $collection);
            $collection = $collection[0];
            return self::collections_folder."/{$collection}.php";
        }
        return '';
    }

    private function attemptInclude(): void {
        if(!is_array($this->collection)) {
            throw new Exception("Colecao {$this->collection_file} precisa ter como return type: array");
        }
    }
    
    public function getCollection(string $chave = '') {
        if(!empty($chave) and !isset($this->collection[$chave])) throw new Exception("Colecao {$this->collection_file} nao possui o item $chave");
        return (empty($chave)) ? $this->handler->get($this) : $this->collection[$chave];
    }

}

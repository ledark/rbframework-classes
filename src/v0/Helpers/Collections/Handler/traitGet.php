<?php

namespace RBFrameworks\Helpers\Collections\Handler;

trait traitGet {
    
    private function getStorage($collection) {
        return $collection->collection ?? include($collection->collecion_file);
    }
    private function getSession($collection) {
        return $_SESSION['collections'][$collection->collection_name] ?? $collection->collection;
    }
    /**
     * Similar a $_SESSION, mas limpa os dados para ser chamado apenas uma vez
     * @param Collection $collection
     * @return array
     */
    private function getMemory($collection) {
        if(isset($_SESSION['collections'][$collection->collection_name])) {
            $collection->collection = $_SESSION['collections'][$collection->collection_name];
            unset($_SESSION['collections'][$collection->collection_name]);
        }
        return $collection->collection;
    }
    private function getPrivate($collection) {
        return $collection->collection;
    }
    private function getGlobals($collection) {
         return $GLOBALS['collections'][$collection->collection_name] ?? $collection->collection;
    } 
    
}

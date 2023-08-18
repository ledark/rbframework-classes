<?php

namespace RBFrameworks\Helpers\Collections\Handler;

trait traitSave {
    
    private function saveStorage($collection) {
        file_put_contents($collection->collection_file, '<?php return '.$collection->var_export($collection->collection, true).';');
    }
    
    private function saveSession($collection) {
        $_SESSION['collections'][$collection->collection_name] = $collection->collection;
    }
    
    private function saveMemory($collection) {
        $_SESSION['collections'][$collection->collection_name] = $collection->collection;
    }
    
    private function savePrivate($collection) {
        
    }
    
    private function saveGlobals($collection) {
        $GLOBALS['collections'][$collection->collection_name] = $collection->collection;
    }
    
}

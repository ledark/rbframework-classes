<?php

namespace RBFrameworks\Helpers\Collections\Handler;

trait traitInit {
    
    private function initStorage($collection) {
        return include($collection->collection_file);
    }
    private function initSession($collection) {
        return $_SESSION['collections'][$collection->collection_name] ?? include($collection->collection_file);
    }
    private function initMemory($collection) {
        return $_SESSION['collections'][$collection->collection_name] ?? include($collection->collection_file);
    }
    private function initPrivate($collection) {
        return include($collection->collection_file);
    }
    private function initGlobals($collection) {
        return $GLOBALS['collections'][$collection->collection_name] ?? include($collection->collection_file);
    }
    
}

<?php

namespace RBFrameworks\Core\Legacy;

use RBFrameworks\Core\Types\File;

class Required
{
    public function __call($method, $args) {
        //Core\Assets\Required
    }

    public static function pathDatabases() {

        

        //$base_path = ecomRaiz.'class/class.';
        $args = func_get_args();
        if(count($args)) {			
            foreach($args as $arg) {
                $className = ucwords($arg);
                if(isset($GLOBALS[$className]) and is_callable($GLOBALS[$className])) return true;

                //SearchFile
                $file = new File($className);
                $file
                    ->addSearchFolders([
                        'class.',
                    ])
                    ->addSearchExtensions([
                        '.class.php'
                    ]);
                if($file->hasFile()) {
                    include($file->getFilePath());
                    if(isset($$className)) {
                        $GLOBALS[$className] = $$className;
                    } else {
                        $GLOBALS[$className] = $$className();
                    }
                    continue;
                }

                if(class_exists($className)) {
                    $GLOBALS[$className] = new $$className;
                }
                /*
                if(file_exists($base_path.$className.'.php')) {
                    include($base_path.$className.'.php');					
                    $GLOBALS[$className] = $$className ;					
                }
                */
            }		    
        }
    }
}

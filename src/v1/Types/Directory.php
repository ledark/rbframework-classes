<?php 

namespace Framework\Types;

class Directory {

    private $path;

    public function __construct(string $path) {
        $this->path = self::trimPath($path);
    }

    public static function trimPath(string $path):string {
        $path = trim($path);
        $path = trim($path, '/');
        $path = trim($path, '\\');
        return $path;
    }

    public function isValidDir():bool {
        return is_dir($this->path);
    }
    
    public function getDirectory():string {
        return empty($this->path) ? './' : $this->path;
    }    

    public function getDirectoryWithoutEndSlash(): string {
        $path = $this->getDirectory();
        $path = self::trimPath($path);
        return $path;
    }

    public function getDirectoryWithEndSlash(): string {
        $path = $this->getDirectory();
        $path = self::trimPath($path);
        return $path.DIRECTORY_SEPARATOR;
    }

    public static function mkdir(string $path, int $mode = 0755, bool $recursive = true):void {
		$path = str_replace('\\', '/', $path);
		$path = ltrim($path, '/');
        if(!is_dir($path)) {        
            $parts = explode('/', $path);
            foreach($parts as $key => $part) {
                if($part == '') continue;
                $path = implode('/', array_slice($parts, 0, $key+1));
                if(!is_dir($path)) {
                    mkdir($path, $mode, $recursive);
                }
            }
        }

        if(!is_dir($path)) {
            throw new \Exception("Directory {$path} not exists");
        }
    }

    public static function rmdir(string $path, bool $deltree = false):void {
        if(is_dir($path)) {
            $parts = explode('/', $path);
            foreach($parts as $key => $part) {
                if($part == '') continue;
                $path = implode('/', array_slice($parts, 0, $key+1));
                if(is_dir($path)) {
                    self::deltree($path, $deltree);
                }
            }
        }
    }
    
    private static function deltree(string $src, bool $deltree = true) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    self::deltree($full);
                }
                else {
                    return;
                    if($deltree) unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }

    public static function interact(string $path, callable $callback):void {
        if(is_dir($path)) {
            $dir = opendir($path);
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    $full = $path . '/' . $file;
                    if ( is_dir($full) ) {
                        self::interact($full, $callback);
                    }
                    else {
                        $callback($full);
                    }
                }
            }
            closedir($dir);
        }
    }

}
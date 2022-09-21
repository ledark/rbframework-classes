<?php 

namespace RBFrameworks\Core\Types;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Utils\Replace;
use RBFrameworks\Core\Utils\Strings\Dispatcher;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class File {

    //Declarations
    public $search = [
        'folder' => [],
        'prefix' => [],
        'extension' => [],
    ];
    public $name = '';
    private $original_name = '';
    private $filepath = '';
    private $folderpath = '';
    private $extension = '';
    private $handleDirectory = null;
    public $replaces = [];
    public $cache = null;
    
    //Constructor
    public function __construct(string $string, array $replaces = [], bool $cacheResult = false) {
        $this->cache = $cacheResult;
        $this->name = $string;
        $this->original_name = $string;
        $this->replaces = $replaces;

        $this->sanitizeName();
        $this->generateDefaultSearchLocations();
        if(is_object($this->preventDirectory())) return $this->preventDirectory();
    }

    public function preventDirectory() {
        if($this->hasDir()) { 
            $this->handleDirectory = new Directory($this->name);
            return $this->handleDirectory;
        }
    }    

    private function generateDefaultSearchLocations() {

        $search_folders = [
            '',
            '/',
            '../',
            '../template/',
            '../../',
        ];
        foreach($search_folders as $folder) {
            $this->addSearchFolder($folder);
        }
        $search_extensions = [
        '',
        '.php',
        '.html',
        '.css',
        '.js',
    ];
        foreach($search_extensions as $extension) {
            $this->addSearchExtension($extension);
        }
        

        $this
            ->addSearchFolder( dirname(debug_backtrace()[0]['file']).'/' )
            ->addSearchFolder( dirname(debug_backtrace()[1]['file']).'/' )
            ->addSearchFolder( dirname(debug_backtrace()[0]['file']).'/../' )
            ->addSearchFolder( dirname(debug_backtrace()[1]['file']).'/../' )
            ->addSearchPrefix('')
        ;
    }

    //Utils
    private function sanitizeName() {
        $this->name = str_replace('\\', '/', $this->name);
        $this->name = str_replace('//', '/', $this->name);
    }

    public function getOriginalName():string {
        return $this->original_name;
    }    

    //Handle:Folders

    public function __call($name, $arguments) {
        if(!is_null($this->handleDirectory)) {
            return call_user_func_array([$this->handleDirectory, $name], $arguments);
        } else {
            return false;
        }
    }

    public function isDir() {
        return false;
    }    

    public function clearSearchFolders():object {
        $this->search['folder'] = [];
        return $this;
    }

    public function addSearchFolder(string $folder):object {
        $this->search['folder'][] = $folder;
        return $this;
    }

    public function addSearchFolders(array $folders):object {
        $this->search['folder'] = array_merge($this->search['folder'], $folders);
        return $this;
    }    

    public function getSearchFolders():array {
        return $this->search['folder'];
    }

    //Handle:Prefixes

    public function clearSearchPrefixes():object {
        $this->search['prefix'] = [];
        return $this;
    }

    public function addSearchPrefix(string $prefix):object {
        $this->search['prefix'][] = $prefix;
        return $this;
    }

    public function addSearchPrefixes(array $prefixes):object {
        $this->search['prefix'] = array_merge($this->search['prefix'], $prefixes);
        return $this;
    }

    public function getSearchPrefixes():array {
        return $this->search['prefix'];
    }     

    //Handle:Extensions

    public function clearSearchExtensions():object {
        $this->search['extension'] = [];
        return $this;
    }

    public function addSearchExtension(string $extension):object {
        $this->search['extension'][] = $extension;
        return $this;
    }

    public function addSearchExtensions(array $extensions):object {
        $this->search['extension'] = array_merge($this->search['extension'], $extensions);
        return $this;
    }

    public function getSearchExtensions():array {
        return $this->search['extension'];
    }

    public function findInParents(int $levels = 3):string {

        //Variaveis
        $prefixes = [''];
        
        $generatePrefix = function(int $level):string {
            $prefix = '';
            for($i=0; $i<$level; $i++) {
                $prefix.= '../';
            }
            return $prefix;
        };
        
        
        for($i=0; $i<$levels; $i++) {
            $prefixes[] = $generatePrefix($i);
        }

        $orginalName = $this->name;
        foreach($prefixes as $prefix) {
            $this->name = $prefix.$orginalName;
            if($this->hasFile()) {
                return $this->name;
            }
        }
        $this->name = $orginalName;
        return $this->name;
    }    

    //Pos:Run

    public function getFolderPath():string {
        if(empty($this->folderpath)) $this->getFilePath();
        return $this->folderpath;
    }

    public function getFileContents():string {
        return $this->hasFile() ? file_get_contents($this->getFilePath()) : '';
    }




    
    public function getFilePath($applyRealPath = false):string {
        if(empty($this->filepath)) {
            foreach($this->search['folder'] as $folder) {
                foreach($this->search['prefix'] as $prefix) {
                    foreach($this->search['extension'] as $extension) {
                        $file = $folder.$this->name.$extension;
                        if(file_exists($file)) {
                            $this->filepath = ($applyRealPath) ? realpath($file) : $file;
                            $this->folderpath = dirname(realpath($file));
                            $this->extension = $extension;
                            return $file;
                        }
                    }
                }
            }
        } else { 
            return $this->filepath; 
        }
        return '';
    }

    public function getExtension():string {
        if(empty($this->filepath)) $this->getFilePath();
        if(empty($this->extension) and strpos($this->filepath, '.') !== false) {
            $parts = explode('.', $this->filepath);
            $this->extension = '.'.end($parts);
        }
        return $this->extension;
    }

    public function getName():string {
        return basename($this->name);
    }

    public function hasDir():bool {
        return (!empty($this->getFilePath()) and is_dir($this->getFilePath())) ? true : false;
    }

    public function hasFile():bool {
        $hasFile = empty($this->getFilePath()) ? false : true;
        if($hasFile and $this->hasDir()) return false;
        return $hasFile;
    }    

    public function __toString():string {
        return $this->getFilePath();
    }

    public function render(bool $capture = false) {
        
        $replaces = $this->replaces;
        $cachekey = Dispatcher::file($this->getFilePath()).md5(serialize($replaces));
        $cache = new FilesystemAdapter(); 
        $content = $cache->get($cachekey, function (ItemInterface $item) use ($replaces) {
            $item->expiresAfter(3600);
            
            ob_start();
            if(!empty($this->getFilePath())) include($this->getFilePath());
            $content = ob_get_clean();
            
            if(count($this->replaces)) {
                $content = Replace::replace($content, $this->replaces);
            }
        
            return $content;
        });
        
        if(!$this->cache) {
            $cache->delete($cachekey);
        }

        if($capture) {
            return $content;
        }

    }

    /**
     * static needsFile retorna o objeto File, ou falha se não conseguir.
     *
     * @param string $filePath
     * @param string|null $message
     * @return File
     */
    public static function needsFiles(string $filePath, string $message = null): File {
        $message = is_null($message) ? "File {$filePath} not found" : $message;
        $fileObject = new self($filePath);
        if(!$fileObject->hasFile()) throw new \Exception($message);
        return $fileObject;
    }

    public static function existsFile(string $filePath):bool {
        return (new self($filePath))->hasFile();
    }
    
    public static function get_file_contents(string $filePath):string {
        return (new self($filePath))->render(true);
    }    
    
}
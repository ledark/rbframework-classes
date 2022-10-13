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
                        if(file_exists($file) and !is_dir($file)) {
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

    public function renderCache(bool $capture = false) {

        $obj = $this;
        $cachekey = Dispatcher::file($this->getFilePath()).md5(serialize($this->replaces));

        $cache = new FilesystemAdapter(); 
        $content = $cache->get($cachekey, function (ItemInterface $item) use ($obj) {
            $item->expiresAfter(3600);                
            return $this->render();
        });
        
        if(!$this->cache) {
            $cache->delete($cachekey);
        }

        if($capture) {
            return $content;
        }
    }

    public function render(bool $capture = false) {
        
        ob_start();
        if(!empty($this->getFilePath())) include($this->getFilePath());
        $content = ob_get_clean();
        
        if(count($this->replaces)) {
            $content = Replace::replace($content, $this->replaces);
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

    private static function getMimeTypes():array {
        return [
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',

            // fonts
            'woff2' => 'application/octet-stream',            
        ];
    }
    
    public static function getMimeType(string $filename) {
        $mime_types = self::getMimeTypes();
		$exp = explode('.',$filename);
        $ext = strtolower(array_pop($exp));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }

    public static function readFile(string $filename) {
        if(!file_exists($filename)) return false;
        if(is_dir($filename)) return false;        
        $mime = self::getMimeType($filename);
        header('Content-Type: '.$mime);
        header('Content-Length: ' . filesize($filename));
        if(strpos($mime, 'application/') !== false) header("Content-Transfer-Encoding: Binary");
        readfile($filename);
        exit();
    }

    public static function createFileDummy(string $filename, int $size = 0) {
        
        if(!is_dir(dirname($filename)) and is_readable(dirname($filename))) {
            throw new \Exception("Directory ".dirname($filename)." not found or not readable");
        }

        if($size === 0) {
            return touch($filename);
        }

        // 32bits 4 294 967 296 bytes MAX Size
        $f = fopen($filename, 'wb');
        if($size >= 1000000000)  {
            $z = ($size / 1000000000);       
            if (is_float($z))  {
                $z = round($z,0);
                fseek($f, ( $size - ($z * 1000000000) -1 ), SEEK_END);
                fwrite($f, "\0");
            }       
            while(--$z > -1) {
                fseek($f, 999999999, SEEK_END);
                fwrite($f, "\0");
            }
        }
        else {
            fseek($f, $size - 1, SEEK_END);
            fwrite($f, "\0");
        }
        fclose($f);
        return true;
    }

}
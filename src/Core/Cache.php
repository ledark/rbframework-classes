<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Exceptions\CollectionException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * $varname = new Cache('cache_name', 3600);
 * if(is_null($varname)) {
 * ...
 * ...Long block of code
 * ...
 * $varname->save($result);
 * }d
 */
class Cache {

    private $cache_folder = null;

    public function __construct(string $id = null, int $expires = 86400) {
        if(is_null($id)) {
            if(isset(debug_backtrace()[0]['file'])) {
                $id = md5(debug_backtrace()[0]['file']);
            } else {
                $id = 'no-cache-name'.uniqid().'-'.date('YmdHis');
            }
        }
        $this->id = $id;
        
        $location_cache = Config::get('location.cache.default');
        if(!is_string($location_cache)) CollectionException::throw('location.cache.default is not a string');
        $this->setCacheFolder($location_cache);
        if($expires > 1000000) {
            $this->expiresWhen($expires);
        } else {
            $this->expiresAfter($expires);
        }        
    }

    /**
     * @sample
     * $value = Cache::stored(function(){ 
     *      return 'value after long process...  
     * });
     * @param string $callback que deve retornar o valor processado
     * @param string $cachedid opcional com o nome do cachedid
     * @param int $ttl in seconds, default to 3600 [1 hour]
     * @return mixed
     */
	public static function stored(callable $callback, string $cacheid = null, int $ttl = 3600) {
		if(is_null($cacheid)) $cacheid = md5(serialize(debug_backtrace(2)));
        return (new FilesystemAdapter('symfony', $ttl, Config::get('location.cache.default')))->get($cacheid, function (ItemInterface $item) use ($callback) {
			return $callback();
        });		
	}

    private function hasOpcache() {
        return is_array(opcache_get_configuration()) ? true : false;
    }

    public function setCacheFolder(string $directory) {
        if(!is_dir($directory)) CollectionException::throw("cache folder $directory is not exists");
        $this->cache_folder = $directory;
    }

    public function save($mixed):void {
        $this->set($mixed);
    }

    public function set($value) {
        $data = serialize($value);
        $data = base64_encode($data);
        file_put_contents($this->getCacheFolder().$this->getKey(), $data);
    }

    public function get() {
        if(!$this->isHit()) return null;
        $data = file_get_contents($this->getCacheFolder().$this->getKey());
        $data = base64_decode($data);
        $data = unserialize($data);
        return $data;
    }

    public function setAsString(string $data) {
        file_put_contents($this->getCacheFolder().$this->getKey(), $data);
    }
    public function getAsString():string {
        return file_get_contents($this->getCacheFolder().$this->getKey());
    }

    public function isHit():bool {
        return file_exists($this->getCacheFolder().$this->getKey()) ? true : false;
    }

    public function getFilename():string {
        return $this->getCacheFolder().$this->getKey();
    }

    public function exists():bool {
        return $this->isHit();
    }

    public function getKey() {
        return $this->id;
    }

    public function getCacheFolder():string {
        return $this->cache_folder;
    }

    public function expiresAfter(int $seconds) {
        if($this->isHit()) {
            $filemtime = filemtime($this->getCacheFolder().$this->getKey());
            $filettl = time() - $filemtime;
            if($filettl > $seconds) {
                unlink($this->getCacheFolder().$this->getKey());
            }
        }
    }

    /**
     * $timestamp geralmente é um data, como a que vinda pelo banco de dados, que diz a última vez em que aquele conteúdo foi atualizado.
     * Ou seja, se a data do arquivo de cache foi inferior ao timespam, o arquivo é antigo e pode ser removido
     *
     * @param integer $timestamp
     * @return void
     */
    public function expiresWhen(int $timestamp) {
        if($this->isHit()) {
            $filemtime = filemtime($this->getCacheFolder().$this->getKey());
            if($timestamp > $filemtime) {
                unlink($this->getCacheFolder().$this->getKey());
            }
        }
    }

    /**
     * 
     * @return bool true caso tenha excluido o arquivo do cache
     */
    private function clear(): bool {
        /**
         * if(time() - filectime($this->getFilename()) > $this->ttl) {
         *     if(unlink($this->getFilename())) {
         *         return true;
         *     }
         * }
         */
        return false;
    }

    public function clearAll() {
        /*
        $files = glob($this::FOLDER.$this::PREFIX.'*');
        $timenow = time();
        foreach($files as $file) {
            if($timenow - filectime($file) > $this->ttl) {
                unlink($file);
                continue;
            }
        }
        */
    }    

    public function setTTL(int $secs): object {
        $this->expiresAfter($secs);
    //    $this->ttl = $secs;
        return $this;
    }
    
    public function toString($mixed):string {
        if(is_object($mixed)) {
            $mixed = (array) $mixed;
        }
        if(is_array($mixed)) {
            return base64_encode(serialize($mixed));
        } else {
            return (string) $mixed;
        }
    }    


}
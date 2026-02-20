<?php 

namespace Framework;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class Cache {

    /**
     * @sample
     * $value = Cache::stored(function(){ 
     *      return 'value after long process...  
     * });
     * @param callable $callback que deve retornar o valor processado
     * @param string $cachedid opcional com o nome do cachedid
     * @param int $ttl in seconds, default to 3600 [1 hour]
     * @return mixed
     */
	public static function stored(callable $callback, string $cacheid = null, int $ttl = null) {
        if(is_null($ttl)) $ttl = Config::get('cache.ttl', 3600);
		if(is_null($cacheid)) $cacheid = md5(serialize(debug_backtrace(2)));
        $cachenamespace = Config::get('cache.namespace', 'symfony');
        $cachedirectory = Config::get('location.cache.default');
        $cacheObject = new FilesystemAdapter($cachenamespace, $ttl, $cachedirectory);
        if($cacheObject->hasItem($cacheid)) {
            return $cacheObject->getItem($cacheid)->get();
        } else {
            $value = $callback();
            $cacheObject->save($cacheObject->getItem($cacheid)->set($value));
            return $value;
        }
	}

    public static function delete(string $cacheid) {
        $cachenamespace = Config::get('cache.namespace', 'symfony');
        $cachedirectory = Config::get('location.cache.default');
        $ttl = Config::get('cache.ttl', 3600);
        $cacheObject = new FilesystemAdapter($cachenamespace, $ttl, $cachedirectory);
        return $cacheObject->delete($cacheid);
    }
    
}
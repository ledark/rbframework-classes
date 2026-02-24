<?php 

use RBFrameworks\Core\Cache;

if(!function_exists('cache_redis')) {

    function cache_redis():\Redis {
        /*
        if(isset($GLOBALS['redis']) and is_object($GLOBALS['redis'])) {
            return $GLOBALS['redis'];
        }
        */
        if(!class_exists('\Redis')) {
            class Redis {
                public function connect() {
                    throw new \Exception('A extensão redis nao foi encontrada.');
                }

                public function flushDB() {

                }

            }
        }
        $redis = new \Redis();
        try {
            $redis->connect('127.0.0.1', 6379, 2.5); // timeout opcional de 2.5s
            //$redis->flushDB();
            //$GLOBALS['redis'] = $redis;
        } catch(\Throwable $e) {

        }
        return $redis;
    }

    function cache_stored_redis(callable $callback, ?string $cache_id = null, ?int $ttl = null): mixed {
        $redis = cache_redis();

        if (is_null($cache_id)) {
            $cache_id = md5(serialize(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)));
        }

        $cache_table_id = cache_table($cache_id);
        if (!empty($cache_table_id)) {
            $cache_id = $cache_table_id;
        }

        $ttl = is_null($ttl) ? 3600 : $ttl;

        try {
            if($redis->exists($cache_id)) {
                $result = $redis->get($cache_id);
                return $result === false ? false : unserialize($result);
            }

            $result = $callback();
            $redis->set($cache_id, serialize($result), ['ex' => $ttl]);
            return $result;

        } catch(\Throwable $e) {
            try { $redis->del($cache_id); } catch (\Throwable) {}
            $result = $callback();
            return $result;
        }
    }

    function cache_remove_redis(string $cache_id):void {
        try {
            $redis = cache_redis();

            $cache_table_id = cache_table($cache_id);
            if (!empty($cache_table_id)) {
                $cache_id = $cache_table_id;
            }

            $redis->del($cache_id);
        } catch(\Throwable $e) {
            $redis->flushDB();
        }
    }

    function cache_stored_old(callable $callback, ?string $cache_id = null, ?int $ttl = null):mixed {

        if(is_null($cache_id)) {
            $cache_id = md5(serialize(debug_backtrace(2)));
        }

        $cache_table_id = cache_table($cache_id);
        if(!empty($cache_table_id)) {
            $cache_id = $cache_table_id;
        }

        $ttl = is_null($ttl) ? 3600 : $ttl;
        return Cache::stored($callback, $cache_id, $ttl);
    }

}

if(!function_exists('cache_table')) {
    /**
     * Generates a cache ID based on the table name.
     *
     * @param string $tablename
     * @return string
     */
    function cache_table(string $tablename):string {
        try {
            $tablename = str_replace('?_', config('database.prefixo'), $tablename);
            $cache_id = config('cachedb.'.$tablename, '').config('database.database');
            if($cache_id == config('database.database')) {
                return '';
            }
            return $cache_id;
        } catch(\Exception $e) {
            return '';
        }
    }
}

if(!function_exists('cache_stored')) {
    /**
     * Stores the result of the callback function in the cache.
     *
     * @param callable $callback
     * @param string|null $cache_id
     * @param int|null $ttl
     * @return mixed
     */
    function cache_stored(callable $callback, ?string $cache_id = null, ?int $ttl = null):mixed {

        if(class_exists('\Redis')) {
            return cache_stored_redis($callback, $cache_id, $ttl);
        }

        if(is_null($cache_id)) {
            $cache_id = md5(serialize(debug_backtrace(2)));
        }

        $cache_table_id = cache_table($cache_id);
        if (!empty($cache_table_id)) {
            $cache_id = $cache_table_id;
        }

        $ttl = is_null($ttl) ? 3600 : $ttl;
        return Cache::stored($callback, $cache_id, $ttl);
    }
}

if(!function_exists('cache_remove')) {
    /**
     * Removes a cache by its ID.
     *
     * @param string $cache_id
     * @return void
     */
    function cache_remove(string $cache_id):void {
        if(class_exists('\Redis')) {
            cache_remove_redis($cache_id);
        }
        $cache_table_id = cache_table($cache_id);
        if (!empty($cache_table_id)) {
            $cache_id = $cache_table_id;
        }

        Cache::delete($cache_id);
    }
}
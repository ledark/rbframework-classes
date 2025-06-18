<?php 

use eftec\bladeone\BladeOne;
use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Cache;

if(!function_exists('get_root_path')) {
    function get_root_path(string $sufix = "") {
        $root = $_SERVER['DOCUMENT_ROOT'];
        $root = str_replace('\\', '/', $root);
        $root = rtrim($root, '/');
        if(!empty($sufix)) {
            $root.= '/'.ltrim($sufix, '/');
        }
        return $root;
    }
}

if(!function_exists('blade')) {
    /**
     * Undocumented function
     *
     * @param string $component
     * @param array $variables
     * @param array $options
     * @param options string views = 'path/to/views/';
     * @param options bool capture = false; // if true, returns the blade content instead of echoing
     * @return void
     */
    function blade(string $component, array $variables = [], array $options = []) {
        blade2($component, $variables, $options);

        $cacheId = $component.md5(serialize($variables)).md5(serialize($options));

        if(!isset($options['views'])) {
            $options['views'] = Cache::stored(function() use ($component) {
                $searchDirectories = Debug::getFileBacktrace();
                foreach($searchDirectories as $searchDirectory) {
                    $searchDirectory = dirname($searchDirectory);
                    if(file_exists($searchDirectory.'/'.$component.'.blade.php')) {
                        return $searchDirectory.'/';
                    }
                }
            }, $cacheId, 60*60*24*30);
        }

        $cache = get_root_path().Config::assigned('location.cache.bladeone', 'log/cache/bladeone');

        $blade = new BladeOne($options['views'], $cache, BladeOne::MODE_DEBUG); // MODE_DEBUG allows to pinpoint troubles.
        $blade->pipeEnable=true;

        if(isset($options['includeScope']) && is_bool($options['includeScope'])) {
            $blade->includeScope = $options['includeScope'];
        }

        if(isset($options['directives']) && is_array($options['directives'])) {
            foreach($options['directives'] as $directive => $callback) {
                $blade->directive($directive, $callback);
            }
        }

        if(isset($options['includeAliases']) && is_array($options['includeAliases'])) {
            foreach($options['includeAliases'] as $alias => $path) {
                $blade->addInclude($path, $alias);
            }
        }

        //Rendering
        if(isset($options['capture']) && $options['capture'] === true) {
            return $blade->run($component, $variables); // it calls /views/hello.blade.php
        } else {
            echo $blade->run($component, $variables); // it calls /views/hello.blade.php
        }
    }


    function blade2(string $component, array $variables = [], array $options = []) {
        echo $component;
    }

}

/**
 *     protected function compileAuth($expression = ''): string
    {
        $expression = $this->stripParentheses($expression);
        if ($expression) {
            $roles = '"' . implode('","', explode(',', $expression)) . '"';
            return $this->phpTag . "if(isset(\$this->currentUser) && in_array(\$this->currentRole, [$roles])): ?>";
        }
        return $this->phpTag . 'if(isset($this->currentUser)): ?>';
    }

    This way we can do this:

     @auth(superadmin,admin)
                <a class="btn btn-outline-primary btn-sm" href="@asset('x/y/edit')">Edit</a>
     @endauth
 *
 */
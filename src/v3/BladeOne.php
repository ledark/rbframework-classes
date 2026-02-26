<?php

namespace RBFrameworks;

use eftec\bladeone\BladeOne as BladeOneBase;
use RBFrameworks\Core\Auth;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Debug;

class BladeOne extends BladeOneBase {

    private array $options;

    public function setOptions(array $options) {
        $this->options = $options;
    }

    public function compileIncScript($expression) {
        $result = $this->compileInc($expression);
        return '<script>'.$result.'</script>';
    }

    public function compileInc($expression) {
        $args = $this->getArgs($expression);
        $path = key($args);
        $path = str_replace(['"', "'"], ['', ""], $path);
        $inc = rtrim($this->options['views'], DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$path;
        if(!file_exists($inc)) {
            $file = rtrim($inc, ',');
            $file = str_replace('.', DIRECTORY_SEPARATOR, $file);
            $file = basename($file);

            $result = '$result = array';
            foreach (\token_get_all($expression) as $token) {
                $result .= \is_array($token) ? $this->parseToken($token) : $token;
            }
            eval($result.';');
            $replaces = $result[1]??[];

            $file = new File($file, $replaces);
            $file->addSearchFolders([$this->options['views'].'/']);
            $file->addSearchExtensions(['.blade.php', '.php']);
            if($file->hasFile()) {
                $content = $file->render(true);
                return $this->runString($content);
            }
        }
        ob_start();
        include $inc;
        $content = ob_get_clean();
        return $content;
   }

   /*
    public function compileFooter() {
        $this->directive()
        ob_start();
    }
    public function compileEndfooter() {
        $response = ob_get_clean();
    }
    */
    private static function renderBlade(string $component, array $variables = [], array $options = []) {
        Autoload::loadFunction('cache');

        $directives = isset($options['directives']) ? $options['directives'] : [];
        unset($options['directives']);
        $cacheId = $component.md5(serialize($variables)).md5(serialize($options));

        if(!isset($options['views'])) {
            $options['views'] = cache_stored(function() use ($component) {
                $component = str_replace('.', DIRECTORY_SEPARATOR, $component);
                $searchDirectories = Debug::getFileBacktrace();

                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                $extractDirectories = [];
                foreach ($trace as $frame) {
                    if (isset($frame['file'])) {
                        $extractDirectories[] = $frame['file'];
                    }
                }

                $searchDirectories = array_merge($searchDirectories, $extractDirectories, [

                ]);

                foreach($searchDirectories as $searchDirectory) {
                    $searchDirectory = dirname($searchDirectory);
                    if(file_exists($searchDirectory.DIRECTORY_SEPARATOR.$component.'.blade.php')) {
                        return $searchDirectory.DIRECTORY_SEPARATOR;
                    }
                }

                return dirname(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[7]['file']);
            }, $cacheId, 60*60*24*30);
        }

        $cache = get_root_path().config('location.cache.bladeone', 'log/cache/bladeone');

        $blade = new BladeOne($options['views'], $cache, BladeOne::MODE_DEBUG); // MODE_DEBUG allows to pinpoint troubles.
        $blade->pipeEnable=true;


        if(isset($options['includeScope']) && is_bool($options['includeScope'])) {
            $blade->includeScope = $options['includeScope'];
            unset($options['includeScope']);
        }

        foreach($directives as $directive => $callback) {
            $blade->directiveRT($directive, $callback);
        }
        unset($options['directives']);

        if(isset($options['composer']) && is_array($options['composer'])) {
            foreach($options['composer'] as $composer) {
                $blade->composer($composer['component'], $composer['callback']);
            }
            unset($options['composer']);
        }

        if(isset($options['includeAliases']) && is_array($options['includeAliases'])) {
            foreach($options['includeAliases'] as $alias => $path) {
                $blade->addInclude($path, $alias);
            }
            unset($options['includeAliases']);
        }


        $blade->setOptions($options);
        $blade->directive('footer', function() {
            return '<?php ob_start(); ?>';
        });
        $blade->directive('endfooter', function() {
            return '<?php $response = ob_get_clean(); \RBFrameworks\Core\Assets::Render("body.end", $response); unset($response); ?>';
        });

        $content = $blade->run($component, $variables);
        foreach($variables as $key => $value) {
            if(is_string($value)) {
                $content = str_replace('{'.$key.'}', $value, $content);
            }
        }

        //Rendering
        if(isset($options['capture']) && $options['capture'] === true) {
            return $content;
        } else {
            echo $content;
        }
    }

    public static function render(string $view, $injector = []):string {

        $scriptFile = str_replace('.', '/', $view);
        //$scriptFile = get_root_path('_app/class/Auditor/Views/') . $scriptFile . '.php';
        if(file_exists($scriptFile)) {
            include $scriptFile;
        }

        $includeAliases = collection('blade.includeAliases');

        $content = self::renderBlade($view, array_merge($includeAliases, $injector), [
            'views' => collection('blade.views'),
            'capture' => true,
            'includeScope' => true,
            'includeAliases' => $includeAliases,
        ]);
        $content = str_replace(['{httpSite}'], [config('server.base_uri')], $content);

        $content = str_replace(array_map(function($key) {
            return '{'.$key.'}';
        }, array_keys($includeAliases)), array_values($includeAliases), $content);

        return $content;
    }

}
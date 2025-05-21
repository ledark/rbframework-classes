<?php 

namespace Framework\View;

use eftec\bladeone\BladeOne as BladeOneBase;
use Framework\Types\File;
use Framework\Debug;
use Framework\Cache;
use Framework\Config;


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

/**
 * Undocumented function
 *
 * @param string $component
 * @param array $variables
 * @param array $options
 * @param options string views = 'path/to/views/';
 * @param options bool capture = false; // if true, returns the blade content instead of echoing
 * @return void|string
 */
public static function renderBlade(string $component, array $variables = [], array $options = []) {

    $variablesSanitized = [];
    foreach($variables as $key => $value) {
        if(is_callable($value)) {
            $value = $value();
        }
        $variablesSanitized[$key] = $value;
    }
    $variables = $variablesSanitized;

    $directives = isset($options['directives']) ? $options['directives'] : [];
    unset($options['directives']);
    $cacheId = time().$component.md5(serialize($variables)).md5(serialize($options));

    if(!isset($options['views'])) {
        $options['views'] = Cache::stored(function() use ($component) {
            $component = str_replace('.', DIRECTORY_SEPARATOR, $component);
            $searchDirectories = Debug::getFileBacktrace();
            foreach($searchDirectories as $searchDirectory) {
                $searchDirectory = dirname($searchDirectory);
                if(file_exists($searchDirectory.DIRECTORY_SEPARATOR.$component.'.blade.php')) {
                    return $searchDirectory.DIRECTORY_SEPARATOR;
                }
            }
            return dirname(debug_backtrace()[7]['file']);
        }, $cacheId, 60*60*24*30);
    }

    $cache = get_root_path().Config::get('location.cache.bladeone', 'log/cache/bladeone');
    if(!is_dir($cache)) {
        mkdir($cache, 0777, true);
    }

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
}
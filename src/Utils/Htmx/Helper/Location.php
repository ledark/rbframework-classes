<?php 

namespace Framework\Utils\Htmx\Helper;

use Framework\Config;
use Framework\Utils\Htmx\Constants;
use Framework\Utils\Htmx\HtmxBootstrap;
use Framework\Utils\Htmx\HtmxComponent;
use Framework\View\BladeOne;

class Location {

    public static function getTypeFromPath(string $filepath):int {
        if(!file_exists($filepath)) {
            return Constants\Type::NOT_FOUND;
        }
        if(strpos($filepath, '.blade.php') !== false) {
            return Constants\Type::BLADE_CONTENT;
        }
        $content = file_get_contents($filepath);
        if(strpos($content, '<?php') !== false) {
            $type = Constants\Type::MIXED_CONTENT;
            if(strpos($content, 'class ') !== false) {
                $type = Constants\Type::CLASS_CONTENT;
                if(strpos($content, 'extends HtmxComponent') !== false) {
                    $type = Constants\Type::CLASS_COMPONENT_CONTENT;
                }
            }
        } else {
            return Constants\Type::HTML_CONTENT;
        }
        if($type == Constants\Type::CLASS_CONTENT or $type == Constants\Type::CLASS_COMPONENT_CONTENT) {
            foreach(HtmxBootstrap::getConfig()['searchNamespaces'] as $searchNamespace) {
                $searchNamespace = rtrim($searchNamespace, '\\');
                if(strpos($content, 'namespace '.$searchNamespace) !== false) {
                    return Constants\Type::HTMX_COMPONENT;
                }
            }
        }
        return $type;
    }

    public static function getComponenentFromName(string $name):HtmxComponent {
        $name = Strings::toPascalCase($name);
        foreach(HtmxBootstrap::getConfig()['searchNamespaces'] as $searchNamespace) {
            $searchNamespace = rtrim($searchNamespace, '\\');
            $class = $searchNamespace.'\\'.ltrim($name, '\\');
            if(class_exists($class)) {
                return new $class();
            }
        }
    }

    public static function getComponenentFromBlade(string $path, array $replaces = [], array $injector = []):HtmxComponent {
        $defaultReplaces = Config::get('htmx.defaultReplaces', []);
        $componentName = basename($path, '.blade.php');

        $file = rtrim(dirname($path), '/').'/'.str_replace('.', '/', $componentName).'.php';

        if(file_exists($file)) {
            include $file;
        }

        $content = BladeOne::renderBlade($componentName, array_merge($defaultReplaces, $replaces, $injector), [
            'views' => dirname($path),
            'capture' => true,
        ]);
        return new HtmxComponent($content, null, $replaces, $injector);
    }

    public static function getComponenentFromHtmlFile(string $path, array $replaces = [], array $injector = []):HtmxComponent {
        return new HtmxComponent(file_get_contents($path), null, $replaces, $injector);
    }

    public static function getComponenentFromMixedFile(string $path, array $replaces = [], array $injector = []):HtmxComponent {
        extract($injector);
        ob_start();
        include $path;
        return new HtmxComponent(ob_get_clean(), null, $replaces);
    }

}
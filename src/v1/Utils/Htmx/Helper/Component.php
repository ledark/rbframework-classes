<?php 

namespace Framework\Utils\Htmx\Helper;

use Framework\Utils\Htmx\Constants;
use Framework\Utils\Htmx\HtmxBootstrap;
use Framework\Utils\Htmx\HtmxComponent;
use Framework\Utils\Htmx\Helper\Render;

class Component {

    public static function attr(HtmxComponent $htmxComponent, string $attrName = 'hx-get', string $value = null):string {
        $value = is_null($value) ? HtmxBootstrap::getRoute().'?name='.$htmxComponent->getName() : $value;
        return ' '.$attrName.'="'.$value.'" ';
    }

    public static function wrapped(string $html):string {
        return '<html><head>'.HtmxBootstrap::getScriptSrc().'</head><body>'.$html.'</body></html>';
    }

    public static function get(string $componentName = null, array $replaces = [], array $injector = []):HtmxComponent {
        if(is_null($componentName)) {
            $componentName = $_GET['name']??'';
            $componentName = empty($componentName) ? $_POST['name']??'' : $componentName;
        }
        if($componentName == '') {
            return Render::error('[NameError] Component name not found');
        }

        $match = function($dir, $file, $componentName) use ($replaces, $injector) {
            return match(Location::getTypeFromPath($dir.'/'.$file)) {
                Constants\Type::MIXED_CONTENT              => Location::getComponenentFromMixedFile($dir.'/'.$file, $replaces, $injector),
                Constants\Type::HTML_CONTENT               => Location::getComponenentFromHtmlFile($dir.'/'.$file, $replaces, $injector),
                Constants\Type::BLADE_CONTENT              => Location::getComponenentFromBlade($dir.'/'.$file, $replaces, $injector),
                Constants\Type::CLASS_CONTENT              => Location::getComponenentFromMixedFile($dir.'/'.$file, $replaces, $injector),
                Constants\Type::CLASS_COMPONENT_CONTENT    => Location::getComponenentFromMixedFile($dir.'/'.$file, $replaces, $injector),
                Constants\Type::HTMX_COMPONENT             => Location::getComponenentFromName($componentName, $replaces, $injector),
            };
        };

        $finder = function($dir, $file, $componentName) use($match) {
            if(file_exists($dir.'/'.$file)) {
                return $match($dir, $file, $componentName);
            }
            return null;
        };

        foreach(HtmxBootstrap::getConfig()['searchFolders'] as $dir) {
            $dir = rtrim(str_replace('[name]', $componentName, $dir), '/');
            foreach(HtmxBootstrap::getConfig()['searchFiles'] as $file) {
                $file = str_replace('[name]', $componentName, $file);
                if(file_exists($dir.'/'.$file)) {
                    return $match($dir, $file, $componentName);
                }

                //Tentativa Normal
                $return = $finder($dir, $file, $componentName);
                if($return instanceof HtmxComponent) {
                    return $return;
                }

                //Tentativa PascalCase
                $return = $finder($dir, Strings::toPascalCase($file), $componentName);
                if($return instanceof HtmxComponent) {
                    return $return;
                }

                $return = $finder($dir, str_replace('.', '/', basename($file, '.php')).'.blade.php', $componentName);
                if($return instanceof HtmxComponent) {
                    return $return;
                }

                /*
                $file = Strings::toPascalCase($file);
                if(file_exists($dir.'/'.$file)) {
                    return $match($dir, $file, $componentName);
                }
                */
            }
        }
        return Render::error('Component "'.$componentName.'" not found');
    }

}
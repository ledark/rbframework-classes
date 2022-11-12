<?php 

namespace RBFrameworks\Core;

/**
 * Dependences: 
 *  custom_mime_content_type
 */

use RBFrameworks\Core\Assets\Html as AssetHtml;
use RBFrameworks\Core\Assets\Css as AssetCss;
use RBFrameworks\Core\Assets\Js as AssetJs;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Http;
use RBFrameworks\Core\Utils\Replace;

abstract class Assets {

    protected static function getExtensionsCapsules():array {
        return [
            '' => ['', ''], 
            '.css' => [' <link href="', '" rel="stylesheet">'], 
            '.js' => ['<script src="', '"></script>'], 
            '.jpg' => ['<img src="', '"/>'], 
            '.png' => ['<img src="', '"/>'], 
            '.gif' => ['<img src="', '"/>'], 
            '.webp' => ['<img src="', '"/>'], 
            '.jpeg' => ['<img src="', '"/>'], 
            '.svg' => ['<img src="', '"/>'], 
        ];
    }

    private static function getSearchExtensions():array {
        return array_keys(self::getExtensionsCapsules());
    }

    private static function getSearchFolders():array {
        $folders = Config::get('location.assets');
        return is_array($folders) ? $folders : [];
    }

    public static function src(string $name, array $replaces = []):string {

        $file = new File($name, $replaces);
        foreach(self::getSearchFolders() as $folder) $file->addSearchFolder($folder);
        foreach(self::getSearchExtensions() as $extension) $file->addSearchExtension($extension);
        return $file->getFilePath();
    }    

    public static function contentType(string $filepath) {
        return File::getMimeType($filepath);
    }

    /*
    public static function sri(string $filepath, string $cachefolder = null):string {
        if(strpos($filepath, '//') !== false) {
            $filepath = self::cdn($filepath, $cachefolder);
        }
        if(strpos($filepath, '.') !== false) {
            $parts = explode('.', $filepath);
            $extension = end($parts);
            $extension = '.'.$extension;
            $capsules = self::getExtensionsCapsules();
            if(in_array($extension, array_keys($capsules))) {
                return $capsules[$extension][0]. Http::getSite(). $filepath.$capsules[$extension][1];
            }
        } 
        return '';
    }    

    public static function cdn(string $uri, string $cachefolder):string {
        $Cache = new Cache( 'core.assets.cdn.'.basename($uri) );
        $Cache->setCacheFolder($cachefolder);
        if($Cache->isHit()) {
            $content = $Cache->getAsString();
        } else {
            Plugin::load("guzzle");
            $content = guzzle_get($uri);
            $Cache->setAsString($content);
        }
        return $Cache->getCacheFolder().$Cache->getKey();
    }
    */

    public static function css(string $name, string $attributes = '"'):string {
        $capsule = self::getExtensionsCapsules()['.css'];
        return $capsule[0]. Http::getSite(). self::src($name) . '" ' . $attributes. 'dinamic="true' .$capsule[1];
    }

    public static function js(string $name, string $attributes = '"'):string {
        $capsule = self::getExtensionsCapsules()['.js'];
        return $capsule[0]. Http::getSite(). self::src($name) . '" ' . $attributes. 'dinamic="true' .$capsule[1];  
    }

    public static function image(string $name, string $attributes = '"'):string {
        $capsule = self::getExtensionsCapsules()['.jpg'];
        return $capsule[0]. Http::getSite(). self::src($name) . '" ' . $attributes. 'dinamic="true' .$capsule[1]; 
    }

   //Adicionar para Renderização Posterior

   public static function Render(string $name, string $buffer = null, bool $forceUTF8 = null) {
    global $assets_output_buffer;
    if(!isset($assets_output_buffer)) {
        $assets_output_buffer = [];
        Debug::log('not seted', [], 'assets_output_buffer', 'CoreAssetsRender');
    }
    if(!isset($assets_output_buffer[$name])) {
        $assets_output_buffer[$name] = '';
        Debug::log("$name not seted", [], 'assets_output_buffer', 'CoreAssetsRender');
    }
    if(!empty($buffer)) {
        
        if($forceUTF8 === true) utf8_encode($buffer);
        if($forceUTF8 === false) utf8_decode($buffer);        
        $assets_output_buffer[$name].= $buffer;
        Debug::log('into $name', $assets_output_buffer, 'assets_output_buffer', 'CoreAssetsRender');
    }
    if(is_null($buffer)) {

        $result = new Replace($assets_output_buffer[$name], self::getGlobalReplaces());
        $result->render();        
        
        Debug::log('rendered $name', $assets_output_buffer, 'assets_output_buffer', 'CoreAssetsRender');
        $assets_output_buffer[$name] = '';
        $assets_output_buffer[$name] = null;
        unset($assets_output_buffer[$name]);
    }
}    

/*
    public static function minify_js(string $codejs):string {
        return $codejs;
    }
*/

    private static function RenderInjector(string $component_name, array $replaces = [], string $prefix = "", string $sufix = "", string $scope = 'footer') {

        //$components_directories = Config::get('location.assets');
        $components_directories = [
            '_app/snippets/',
        ];

        $resolveDirectory = function (array &$dir, string $path) {
            $dir[] = dirname($path).'/components/';
            $dir[] = dirname($path).'/../components/';
            $dir[] = dirname($path).'/';
            $dir[] = dirname($path).'/../';
        };
    
        if(isset(debug_backtrace()[0]['file'])) $resolveDirectory($components_directories, debug_backtrace()[0]['file']);
        if(isset(debug_backtrace()[1]['file'])) $resolveDirectory($components_directories, debug_backtrace()[1]['file']);
        if(isset(debug_backtrace()[2]['file'])) $resolveDirectory($components_directories, debug_backtrace()[2]['file']);
        if(isset(debug_backtrace()[3]['file'])) $resolveDirectory($components_directories, debug_backtrace()[3]['file']);        

        $inner = self::componentString($component_name, $replaces, $components_directories);

        //self::copy($component_name.'_'.md5(serialize($replaces)), $inner);

        self::Render($scope, $prefix. $inner .$sufix);
    }

    public static function componentString($name, array $replaces = [], array $component_directories = []):string {
        $component_directories[] = '';
        $component_directories[] = __DIR__.'/../../layout/components/';
        $component_directories[] = __DIR__.'/../../layout/assets/';
        $component_directories[] = dirname(debug_backtrace()[0]['file']).'/components/';    
        ob_start();
        self::component($name, $replaces, $component_directories);
        return ob_get_clean();
    }   
    
    private static function component($name, array $replaces = [], array $component_directories = []) {
        
            
            $component_directories[] = '';
            $component_directories[] = __DIR__.'/../../layout/components/';
        
            $resolveDirectory = function (array &$dir, string $path) {
                $dir[] = dirname($path).'/components/';
                $dir[] = dirname($path).'/../components/';
                $dir[] = dirname($path).'/';
                $dir[] = dirname($path).'/../';
            };
        
            if(isset(debug_backtrace()[0]['file'])) $resolveDirectory($component_directories, debug_backtrace()[0]['file']);
            if(isset(debug_backtrace()[1]['file'])) $resolveDirectory($component_directories, debug_backtrace()[1]['file']);
            if(isset(debug_backtrace()[2]['file'])) $resolveDirectory($component_directories, debug_backtrace()[2]['file']);
            if(isset(debug_backtrace()[3]['file'])) $resolveDirectory($component_directories, debug_backtrace()[3]['file']);
        
            /*
            if(isset(debug_backtrace()[0]['file'])) $component_directories[] = dirname(debug_backtrace()[0]['file']).'/components/';
            if(isset(debug_backtrace()[1]['file'])) $component_directories[] = dirname(debug_backtrace()[1]['file']).'/components/';
            if(isset(debug_backtrace()[2]['file'])) $component_directories[] = dirname(debug_backtrace()[2]['file']).'/components/';
            if(isset(debug_backtrace()[3]['file'])) $component_directories[] = dirname(debug_backtrace()[3]['file']).'/components/';
            */
            foreach($component_directories as $folder) {
                foreach(['', '.php', '.html'] as $extension) {
                    if(file_exists($folder.$name.$extension)) {
                        
                        //Replaces and Render
                        $file = new File($folder.$name.$extension);
                        ob_start();
                        include($file->getFilePath());
                        $content = new Replace(ob_get_clean(), array_merge(self::getGlobalReplaces(), $replaces));
                        echo $content->render(true);                        
                        
                        return;
                    }
                    
                }
            }
            Debug::devCard($component_directories, "Component <big><strong>$name</strong></big> nao encontrado nessas pastas abaixo");
                
    }

    /*
    private static function copy(string $component_name, string $content) {
        file_put_contents("log/cache/component.{$component_name}", $content, 'CoreAssetsRender');
    }
    */

    public static function RenderJs(string $component_name, array $replaces = [], string $scope = 'footer') {
        if(substr($component_name, -3) != '.js') $component_name.= '.js';
        self::RenderInjector($component_name, $replaces, "<script data-rel=\"".$component_name."\">\r\n", "\r\n</script>", $scope);
        /*
        self::Render($scope, "<script>\r\n".
            componentString($component_name, $replaces)
            ."\r\n</script>");
            */
    }

    public static function RenderCss(string $component_name, array $replaces = [], string $scope = 'footer') {
        if(substr($component_name, -3) != '.css') $component_name.= '.css';
        self::RenderInjector($component_name, $replaces, '<style>', '</style>', $scope);
    }

    public static function minify_html($input) {
        return AssetHtml::minify_html($input);
    }
    public static function minify_css($input) {
        return AssetCss::minify_css($input);
    }
    public static function minify_js($input) {
        return AssetJs::minify_js($input);
    }

    public static function addGlobalReplaces(array $replaces) {
        global $assets_replaces;
        if(!isset($assets_replaces) or !is_array($assets_replaces)) $assets_replaces = [];
        $assets_replaces = array_merge($assets_replaces, $replaces);
    }

    public static function getGlobalReplaces():array {
        global $assets_replaces;
        if(!isset($assets_replaces) or !is_array($assets_replaces)) $assets_replaces = [];
        return $assets_replaces;
    }
    
    
        

}
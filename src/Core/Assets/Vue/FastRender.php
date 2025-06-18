<?php 

namespace RBFrameworks\Core\Assets\Vue;

use GuzzleHttp\Psr7\Stream;
use RBFrameworks\Core\Assets\StreamFile;

/**
 * (new FastRender())->run();
 */

 use RBFrameworks\Core\Assets\Js;

class FastRender {

    public $config = [];
    public $components = [];
    public $modules = [];
    public $data = [];
    public $methods = [];
    public $mounted = [];

    public function __construct() {
        $this->config['resolveComponents'] = 'import'; //import|module|strict
    }

    public function setConfig(string $name, $value):object {
        $this->config[$name] = $value;
        return $this;
    }

    public function addComponent(string $name, string $path) {
        $this->components[$name] = $path;
    }

    public function addModule(string $path) {
        $this->modules[] = $path;
    }

    public function addData(string $name, $value) {
        $this->data[$name] = $value;
    }

    public function addMethod(string $name, $value) {
        $this->methods[$name] = $value;
    }

    public function addMounted(string $name, $value) {
        $this->mounted[$name] = $value;
    }

    public function getRenderedModules():string {
        $componentsModules = "";
        $this->modules = array_unique($this->modules);
        foreach($this->modules as $path) {
            $componentsModules .= Js::getTagModule($path);
        }
        if($this->config['resolveComponents'] == 'module') {
            foreach($this->components as $name => $path) {
                $componentsModules .= Js::getTagModule($path);
            }
            $componentsModules .= "\r\n\t\t\t\t";
        }
        return $componentsModules;
    }

    public function getRenderedModulesImportDeclaration():string {
        $componentsModules = "\r\n";
        if($this->config['resolveComponents'] == 'module') {
            foreach($this->components as $name => $path) {
                $componentsModules .= "\t\t\t\timport {$name} from '{$path}';\r\n";
            }
            $componentsModules .= "\r\n\t\t\t\t";
        }
        return rtrim($componentsModules, "\r\n\t\t\t\t");
    }

    public function getRenderedComponents() {
        $components = '';
        foreach($this->components as $name => $path) {
            switch($this->config['resolveComponents']) {
                case 'import':
                    $components .= "{$name}: () => import('{$path}'),";
                break;
                case 'module':
                    $components .= "{$name}: {$name}, ";
                break;
                case 'strict':
                    $components .= $path;
                break;
            }
            $components .= "\r\n\t\t";
        }
        return rtrim($components, "\r\n\t\t");
    }

    public function getRenderedData() {
        $data = '';
        foreach($this->data as $name => $value) {
            if(is_string($value)) {
                $data .= "{$name}: '{$value}',";
            } else
            if(is_numeric($value)) {
                $data .= "{$name}: {$value},";
            } else
            if(is_array($value)) {
                $data .= "{$name}: ".json_encode($value).",";
            } else
            if(is_bool($value)) {
                $data .= "{$name}: ".($value ? 'true' : 'false').",";
            } else
            if(is_callable($value)) {
                $data .= "{$name}: ".$value().",";
            } else {
                $data .= "{$name}: {$value},";
            }
        }
        return $data;
    }

    public function getRenderedMethods() {
        $methods = '';
        foreach($this->methods as $name => $value) {
            $name = trim($name);
            $name = (substr($name, strlen($name)-1, 1) != ')') ? $name.'()' : $name;
            if(file_exists($value)) {
                $value = file_get_contents($value);
            } else {
                $value = 'console.log("'.$name.'", "'.$value.'");';
            }
            $methods .= "{$name} {
                {$value}
            },";
        }
        return $methods;
    }

    public function getRenderedMounted() {
        $mounted = '';
        foreach($this->mounted as $name => $value) {
            $name = trim($name);
            $name = (substr($name, strlen($name)-1, 1) != ')') ? $name.'()' : $name;
            if(file_exists($value)) {
                $value = file_get_contents($value);
            } else {
                $value = 'console.log("'.$name.'", "'.$value.'");';
            }
            $mounted .= "{$name} {
                {$value}
            },";
        }
    }

    public function createApp(string $mountElement) {
        echo $this->getRenderedModules();
        echo '<script type="module">';
        echo "import { createApp } from '".config("server.base_uri")."front/startbootstrap-sb-admin-gh-pages/assets/vue3/vue.esm-browser.js'";
        echo $this->getRenderedModulesImportDeclaration();
        echo "
        createApp({
            components: {
                {$this->getRenderedComponents()}
            },
            data() {
              return {
                {$this->getRenderedData()}
              }
            },
            methods: {
                {$this->getRenderedMethods()}
            },
            mounted() {
                {$this->getRenderedMounted()}
            }
          }).mount('{$mountElement}')
          ";
        echo '</script>';
    }

    public function createAppFromJSFile(string $filepath, array $replaces = [], array $options = []) {
        echo $this->getRenderedModules();
        $replaces = array_merge([
            'renderedModulesImportDeclaration' => $this->getRenderedModulesImportDeclaration(),
            'renderedComponents' => $this->getRenderedComponents(),
            'renderedData' => $this->getRenderedData(),
            'renderedMethods' => $this->getRenderedMethods(),
            'renderedMounted' => $this->getRenderedMounted(),
        ], $replaces);
        StreamFile::jsModule($filepath, $replaces, $options);
    }

    public function run() {
        include(__DIR__.'/Pieces/empty-script.tmpl');
    }
}
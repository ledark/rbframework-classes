<?php 

namespace RBFrameworks\Core\Assets\Vue;

/**
 * (new FastRender())->run();
 */

 use RBFrameworks\Core\Assets\Js;

class FastRender {

    public $config = [];
    public $components = [];
    public $data = [];
    public $methods = [];

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

    public function addData(string $name, $value) {
        $this->data[$name] = $value;
    }

    public function addMethod(string $name, $value) {
        $this->methods[$name] = $value;
    }

    public function getRenderedModules():string {
        $componentsModules = "";
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
                $componentsModules .= "import {$name} from '{$path}';";
            }
            $componentsModules .= "\r\n\t\t\t\t";
        }
        return $componentsModules;
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
            $components .= "\r\n\t\t\t\t";
        }
        return $components;
    }

    public function getRenderedData() {
        $data = '';
        foreach($this->data as $name => $value) {
            if(is_string($value)) {
                $data .= "{$name}: '{$value}',";
            } else {
                $data .= "{$name}: {$value},";
            }
        }
        return $data;
    }

    public function getRenderedMethods() {
        $methods = '';
        foreach($this->methods as $name => $value) {
            $methods .= "{$name}: {$value},";
        }
        return $methods;
    }

    public function createApp(string $mountElement) {
        echo $this->getRenderedModules();
        echo '<script type="module">';
        echo "import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js'";
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
            }
          }).mount('{$mountElement}')
          ";
        echo '</script>';
    }

    public function run() {
        include(__DIR__.'/Pieces/empty-script.tmpl');
    }
}
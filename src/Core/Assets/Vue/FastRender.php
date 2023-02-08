<?php 

namespace RBFrameworks\Core\Assets\Vue;

/**
 * (new FastRender())->run();
 */

class FastRender {

    public $components = [];
    public $data = [];
    public $methods = [];

    public function addComponent(string $name, string $path) {
        $this->components[$name] = $path;
    }

    public function addData(string $name, $value) {
        $this->data[$name] = $value;
    }

    public function addMethod(string $name, $value) {
        $this->methods[$name] = $value;
    }

    public function getRenderedComponents() {
        $components = '';
        foreach($this->components as $name => $path) {
            $components .= "{$name}: () => import('{$path}'),";
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
        echo '<script type="module">';
        echo "import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js'";
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
<?php 

namespace RBFrameworks\Core\Assets;

use RBFrameworks\Core\Assets\StreamFile;

/**
 * (new Vue())->run();
 */
/**
 * Exemplo: Em qualquer html, por exemplo:
 * <div id="app"><contador-exemplo></contador-exemplo></div>
 * Chame: Vue::module('js/vue/app.js');
 * Chame: Vue::module('js/vue/contador-exemplo.js');
 */
class Vue {

    public static function getUri(string $path, array $replaces = []) {
        return (new StreamFile($path, $replaces))->getHttpPath();
    }

    public static function module(string $path, array $replaces = []) {
        echo '<script type="module" src="'.static::getUri($path, $replaces).'"></script>';
    }    

    public $scrips = [
        'global' => 'https://unpkg.com/vue@3/dist/vue.global.js',
    ];

    public $modules = [];

    public $configs = [
        'capture' => false,
        'createScriptTag' => true,
    ];

    public function setConfig(string $name, $value):object {
        $this->configs[$name] = $value;
        return $this;
    }

    public function getConfig(string $name) {
        return $this->configs[$name] ?? null;
    }

    public function run() {
        if($this->getConfig('capture')) ob_start();
        
        //createScriptTag
        if($this->getConfig('createScriptTag')) {
            echo Js::getTagNormal($this->scrips['global']);
        }

        foreach($this->modules as $module) {
            echo Js::getTagModule($module);
        }

        if($this->getConfig('capture')) return ob_get_clean();
    }

}
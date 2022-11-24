<?php 

namespace RBFrameworks\Core\Assets;

/**
 * (new Vue())->run();
 */
class Vue {

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

    public function run():void {
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
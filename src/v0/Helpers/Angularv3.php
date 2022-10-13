<?php

namespace RBFrameworks\Helpers;

/**
 * Inicialmente como trait para incluir dentro da Angularv1
 * A ideia é futuramente ser uma classe independente.
 */

trait Angularv3 {
    

    
}

    /*
    var $beautify = true;
    private function beautify($case = 'br') {
        if($this->beautify) {
            if(is_int($case)) {
                $r = '';
                for($i=0; $i<$case; $i++) {
                    $r.= "\t";
                }
                return $r;
            }
            switch($case) {
                case 'nl':
                    return "\r\n";
                break;
                case 'br':
                    return "\r\n";
                break;
            }
        }
        return '';
    }
    
    public function render_modules() {
        $modules = implode(",", $this->modules);
        $modules = !empty($modules) ? ", [".$modules."]" : "";
        $modules = ($modules == ", ['']") ? ', []' : $modules;
        return $modules;
    }
    public function render_open() {
        return "window.angular.module('$this->app'{$this->render_modules()})".$this->beautify('br');
    }
    public function render_config() {
        $interpolate_symbols = explode('[ANGULAR]', $this->interpolate);
        return $this->beautify(1).".config(function(\$interpolateProvider){
            \$interpolateProvider.startSymbol('{$interpolate_symbols[0]}').endSymbol('$interpolate_symbols[1]');
        })".$this->beautify('br');
    }
    public function render_filter() {
        $ret = '';
        foreach($this->filters as $name => $value) {
            $value = $this->beautify(2).trim($value);
            $ret.= $this->beautify(1).".filter('{$name}', {$value})".$this->beautify('br');
        }
        return $ret;
    }
    public function render_files() {
        $ret = '';
        foreach($this->toLoad as $section => $file) {
            switch($section) {
                case 'controller':
                    $ret.= $this->render_controllers($file);
                break;
                case 'function':
                    $ret.= $this->render_functions($file);
                break;
                case 'directive':
                    $ret.= $this->render_directives($file);
                break;
            }
        }
        return $ret;
    }
    public function render_controllers($config) {
        foreach($config as $file) {
            $return ?? $this->renderAs('controller', $file);
        }
        return $return ?? '';
    }
    public function render_functions($config) {
        foreach($config as $file) {
            $return ?? $this->renderAs('', $file);
        }
        return $return ?? '';
    }
    public function render_directives($config) {
        foreach($config as $file) {
            $return ?? $this->renderAs('directive', $file);
        }
        return $return ?? '';
    }
    
    public function renderAs($type, $content) {
        return $this->beautify(1).".{$type}(function(){ $content })".$this->beautify('br');
    }


    public function render_all() {
        $filename = 'teste';
        ob_start();
        echo $this->render_open();
        echo $this->render_config();
        echo $this->render_filter();
        echo $this->render_files();
        $code = ob_get_clean();
        file_put_contents('log/'.$filename.'.js', $code);
    }
*/

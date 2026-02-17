<?php 

namespace RBFrameworks\Core\Templates\Traits;

trait VarTrait {

    public $var = [];

    public function addVar(string $name, $value = null):object {
        $this->var[$name] = $value;
        return $this;
    }

    public function clearAllVars() {
        $this->var = [];
    }

    public function setVar(array $vars):object {
        $this->var = array_merge($this->var, $vars);
        return $this;
    }

    public function renderVar(string $name) {
        return $this->var[$name];
    }

    public $global = [];

    public function addGlobal(string $name, $value = null):object {
        $this->global[$name] = $value;
        return $this;
    }

    public function clearAllGlobals() {
        $this->global = [];
    }

    public function setGlobal(array $vars):object {
        $this->global = array_merge($this->global, $vars);
        return $this;
    } 

}
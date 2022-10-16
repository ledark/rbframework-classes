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

}
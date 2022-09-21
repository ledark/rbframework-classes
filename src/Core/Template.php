<?php 

namespace RBFrameworks\Core;

use RBFrameworks\Core\Types\File;

class Template {
    
    public $var = [];

    public function addVar(string $name, $value = null) {
        $this->var[$name] = $value;
    }

    public function clearAllVars() {
        $this->var = [];
    }

    public function setVar(array $vars) {
        $this->var = array_merge($this->var, $vars);
    }

    public function renderVar(string $name) {
        return $this->var[$name];
    }

    public function renderPage(string $template, array $var = []) {

        $template = new File($template);

        $this->setVar($var);
        ob_start();
        include $template->getFilePath();
        return ob_get_clean();
    }
}
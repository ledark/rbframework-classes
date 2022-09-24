<?php 

namespace RBFrameworks\Core;

use RBFrameworks\Core\Types\File;

class Template {

    private $templateFile;
    private $templateContent;

    public function __construct(string $template_or_string = null) {
        if(!is_null($template_or_string)) {
            $template_or_string = new File($template_or_string);
            if($template_or_string->hasFile()) {
                $this->templateFile = $template_or_string->getFilePath();
                $this->templateContent = $template_or_string->getFileContent();
            }
            else {
                $this->templateFile = null;
                $this->templateContent = $template_or_string;
            }
        }
    }
    
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

    public function isFile():bool {
        return !is_null($this->templateFile);
    }
}
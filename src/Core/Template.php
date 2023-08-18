<?php 

namespace RBFrameworks\Core;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Templates\Traits\VarTrait;
use RBFrameworks\Core\Templates\Traits\TemplateTrait;
use RBFrameworks\Core\Templates\Traits\PageTrait;

class Template {

    private $originalInput;
    private $templateFile;
    private $templateContent;
    private $search_folders = [];
    private $search_extensions = [];

    use VarTrait;
    use TemplateTrait;
    use PageTrait;

    public function __construct(string $template_or_string = null) {
        $this->originalInput = $template_or_string;
        $this->build($template_or_string);
    }

    public function build(string $template_or_string = null) {
        if(is_null($template_or_string)) {
            $template_or_string = $this->originalInput;
        }
        if(!is_null($template_or_string)) {
            $templateObject = new File($template_or_string);            
            $templateObject->addSearchFolders($this->getSearchFolders());
            $templateObject->addSearchExtensions($this->getSearchExtensions());
            $templateObject->addSearchExtension('.tmpl');
            if($templateObject->hasFile()) {
                $this->templateFile = $templateObject->getFilePath();
                //$this->templateContent = $templateObject->get_file_contents($templateObject->getFilePath());
            } else {
                $this->templateFile = null;
                $this->templateContent = $template_or_string;
            }
        }
        return $this;
    }

    public function isFile():bool {
        return !is_null($this->templateFile);
    }

    private function getSearchFolders():array {
        return array_merge(Config::assigned('location.search_folders', []), $this->search_folders);
    }
    private function getSearchExtensions():array {
        return array_merge(Config::assigned('location.search_extensions', []), $this->search_extensions);
    }
    public function addSearchFolder(string $name):object {
        $this->search_folders[] = $name;
        return $this;
    }
    public function addSearchExtension(string $name):object {
        $this->search_extensions[] = $name;
        return $this;
    }
}
<?php 

namespace RBFrameworks\Core\Templates\Traits;

use RBFrameworks\Core\Types\File;

trait TemplateTrait {

    protected function getTemplateFile():string {
        if(isset($this->templateFile)) {
            return is_null($this->templateFile) ? 'has-no-template-file' : $this->templateFile;
        }
    }
    protected function getTemplateContent():string {
        if(isset($this->templateContent)) {
            return is_null($this->templateContent) ? 'has-no-template-content' : $this->templateContent;
        }
    }

}
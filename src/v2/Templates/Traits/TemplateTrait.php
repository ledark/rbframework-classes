<?php 

namespace RBFrameworks\Core\Templates\Traits;

use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Types\HtmlElement;

trait TemplateTrait {

    protected function getTemplateFile():string {
        if(isset($this->templateFile)) {
            return is_null($this->templateFile) ? 'has-no-template-file' : $this->templateFile;
        } else {
            return '<no-template-file></no-template-file>';
        }
    }
    protected function getTemplateContent():string {
        if(isset($this->templateContent)) {
            return is_null($this->templateContent) ? 'has-no-template-content' : $this->templateContent;
        } else {
            return '<no-template-content></no-template-content>';
        }
    }

}
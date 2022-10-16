<?php 

namespace RBFrameworks\Core;

use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Templates\Traits\VarTrait;
use RBFrameworks\Core\Templates\Traits\TemplateTrait;
use RBFrameworks\Core\Templates\Traits\PageTrait;

class Template {

    private $templateFile;
    private $templateContent;

    use VarTrait;
    use TemplateTrait;
    use PageTrait;

    public function __construct(string $template_or_string = null) {
        if(!is_null($template_or_string)) {
            $template_or_string = new File($template_or_string);
            if($template_or_string->hasFile()) {
                $this->templateFile = $template_or_string->getFilePath();
                $this->templateContent = $template_or_string->get_file_contents($template_or_string->getFilePath());
            }
            else {
                $this->templateFile = null;
                $this->templateContent = $template_or_string;
            }
        }
    }

    public function isFile():bool {
        return !is_null($this->templateFile);
    }
}
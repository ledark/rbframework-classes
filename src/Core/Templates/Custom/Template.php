<?php 

namespace RBFrameworks\Core\Templates\Custom;

use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Templates\Traits\VarTrait;
use RBFrameworks\Core\Templates\Traits\TemplateTrait;
use RBFrameworks\Core\Templates\Traits\PageTrait;

class Template {

    use VarTrait;
    use TemplateTrait;
    use PageTrait;

    public function __construct(string $template) {
        $template = new File($template);
        $this->templateFile = $template->getFilePath();
    }

    public function renderPage() {
        ob_start();
        if(isset($this->page) and file_exists($this->page)) {
            include($this->page);
        } else {
            if(isset($this->var['page'])) {
                $page = $this->var['page'];
            } else
            if(isset($this->var['content'])) {
                $page = $this->var['content'];
            }
            if(!isset($page)) throw new \Exception("Page not found");
            echo $page;
            $page = new File($page);
            if($page->hasFile()) {
                include($page->getFilePath());
            } else {
                echo $page;
            }
        }
        return ob_get_clean();
    }

    public function render() {
        include($this->getTemplateFile());
    }
    
}
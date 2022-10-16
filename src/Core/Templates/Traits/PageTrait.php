<?php 

namespace RBFrameworks\Core\Templates\Traits;

use RBFrameworks\Core\Types\File;

trait PageTrait {

    public function setPage(string $filename):object {
        $this->page = $filename;
        return $this;
    }

    public function renderPage(string $template = null, array $var = []) {
        if(is_null($template)) $template = $this->getTemplateFile();
        $template = new File($template);

        $this->setVar($var);
        ob_start();
        include $template->getFilePath();
        return ob_get_clean();
    }    

}
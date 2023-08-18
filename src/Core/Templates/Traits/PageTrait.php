<?php 

namespace RBFrameworks\Core\Templates\Traits;

use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Types\HtmlElement;

trait PageTrait {

    public function setPage(string $filename):object {
        $this->page = $filename;
        return $this;
    }

    private function renderTemplateFile() {

    

        $template = $this->getTemplateFile();
        $template = new File($template);
        if(!empty($template->getFilePath())) {
            //include($template->getFilePath());            
            include(__DIR__.'/../Legacy/base.php');     
        } else {
            echo $this->getTemplateContent();
        }
    }

    private function renderTemplateContent() {
        $template = $this->getTemplateContent();
        $template = new File($template);
        if(!empty($template->getFilePath())) {
            include(__DIR__.'/../Legacy/base.php');       
        } else {
            echo '';
        }        
    }

    
    public function renderTemplate(string $page = null, array $replaces = []) {
        if(!is_null($page)) $this->setPage($page);
        $this->setVar($replaces);
        $template = $this->getTemplateFile();
        $template = new File($template);

        ob_start();
        $this->renderTemplateFile();
        return ob_get_clean();
    }
    

    //This is Original RenderPage
    
    public function renderPage(string $template = null, array $var = []) {
        if(is_null($template)) $template = $this->getTemplateFile();
        $template = new File($template);

        $this->setVar($var);
        ob_start();
        if(!empty($template->getFilePath())) {
            include $template->getFilePath();
        } else {
            echo $this->getTemplateContent();
        }
        return ob_get_clean();
    }
    
    
    public function displayPage(string $template = null, array $var = []) {
        echo $this->renderPage($template, $var);
    }

}
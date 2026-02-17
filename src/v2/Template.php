<?php 

namespace RBFrameworks\Core;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Templates\Traits\VarTrait;
use RBFrameworks\Core\Templates\Traits\TemplateTrait;
use RBFrameworks\Core\Templates\Traits\PageTrait;

/**
 * Descrição:
 *  Prepara um arquivo qualquer para ser tratato como sendo um template.
 *  Funções como ->setPage('path/to/file) e ->addVar('name', 'value') são utilizadas para preparar o template como um objeto.
 *  Mas não fazem nada. Por isso, o mais útil é o uso dessa classe como uma classe a extender outra.
 *  Um bom exemplo é a classe TemplateController que extende essa classe. Por isso, melhor uso é esse:
 * 
 *  use RBFrameworks\Core\Types\File;
 *  
 *  class Template extends \RBFrameworks\Core\Template {
 *  
 *      public function __construct(string $template = null, array $variables = []) {
 *          $this->setVar($variables);
 *          $this->addSearchFolder(__DIR__.'/../../tmpl/');
 *          $this->addSearchFolder(__DIR__.'/../../../tmpl/');
 *          $this->addSearchFolder(__DIR__.'/../tmpl/');
 *          $this->addSearchExtension('html');
 *          parent::__construct($template);
 *      }
 *  
 *      public function display() {
 *          $template = $this->getTemplateFile();
 *          $template = new File($template);
 *          if(!empty($template->getFilePath())) {       
 *              include($template->getFilePath());   
 *          } else {
 *              echo $this->getTemplateContent();
 *          }
 *      }
 *  
 *  }
 * 
 * Fluxo:
 *  Você inicia a instância passando um template ou não.
 *  Caso você passe uma string, ele vai tentar encontrar o arquivo.
 *  utiliza-se da collection (array) location.search_folders 
 *  utiliza-se da collection (array) location.search_extensions
 *  
 * Example #1: Nothing is passed
 * (new Template())
 * 
 * Example #2: Passing a any valid file (folders and search extensions are optional)
 * (new Template('path/to/file.php'))
 * 
 * Example #2.1 (optional): Adding search folders or extensions
 * (new Template('my_file'))
 *     ->addSearchFolder('path/to/folder')
 *     ->addSearchFolder('path/to/folder2')
 *     ->addSearchExtension('.inc')
 *     ->addSearchExtension('.templ')
 * 
 * Example #3: Passing a string
 * (new Template('<h1>My String</h1>'))
 */

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
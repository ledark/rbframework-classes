<?php 

namespace RBFrameworks\Core\Symfony;

use Symfony\Component\HttpFoundation\Response;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Types\Directory;
use RBFrameworks\Core\Utils\Replace;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use RBFrameworks\Core\Utils\Strings\Dispatcher;

class TemplateController {

    public $page = "";
    public $template_dir = "";
    public $template_file = "";

    /** 
     * For [Template File], its not a [Page File] 
     * Page File is rendered inside Template File
    */
    private function findTemplate(string $withingPageFile = null):bool {
        $template_dirs = array_reverse(Config::get('symfony.template_dir'));
        $template_files = array_reverse(Config::get('symfony.template_filename'));
        $withingPageFile = is_null($withingPageFile) ? $this->getPage() : $withingPageFile;

        //Search for Page File into Teplate Dir
        foreach($template_dirs as $template_dir) {

            $page = new File($template_dir.$withingPageFile);
            if($page->hasFile()) {
                
                $this->template_dir = dirname($template_dir.$withingPageFile).'/';
                $this->template_file = basename($template_dir.$withingPageFile);                
                
                //Search for Template File into Teplate Dir
                foreach($template_files as $template_file) {
                    if(file_exists($template_dir.$template_file)) {
                        $this->template_file = $template_file;
                        return true;
                    }
                }
            }
        }        
        return false;
        
    }

    private function getTemplateDir():string {
        if(!empty($this->template_dir)) {
            return $this->template_dir;
        }
        if($this->findTemplate()) {
            return $this->template_dir;
        } else {
            throw new \Exception("Template not found in: ".implode(', ', Config::get('symfony.template_dir')).' with any names: '.implode(', ', Config::get('symfony.template_filename')));
        }
    }

    private function getTemplateFile():string {
        if(!empty($this->template_file)) {
            return $this->template_file;
        }
        if($this->findTemplate()) {
            return $this->template_file;
        } else {
            throw new \Exception("Template not found in: ".implode(', ', Config::get('symfony.template_dir')).' with any names: '.implode(', ', Config::get('symfony.template_filename')));
        }
    }

    public function getTemplateFileContent(array $injectVariables = [], string $pageName = ""):string {
        $this->findTemplate($pageName);
        ob_start();
        include($this->getTemplateDir().$this->getTemplateFile()); 
        return Replace::replace(ob_get_clean(), $injectVariables);
    }
    
    public function renderTemplate(string $page, $data = [], $config = []) {
        if(!count($data)) $data = [
            'page' => $page,
        ];
        if(!isset($config['cachePage'])) $config['cachePage'] = 0;
        $this->setPage($page);

        $cache = new FilesystemAdapter(); 
        $content = $cache->get(Dispatcher::file($page).md5(serialize($data)), function (ItemInterface $item) use ($data, $config, $page) {
            $item->expiresAfter($config['cachePage']);
            return $this->getTemplateFileContent($data, $page);
        });
        return new Response($content);
    }

    public function render(string $page, $data = [], $config = []) { //alias
        return $this->renderTemplate($page, $data, $config);
    }

    /**
     * For Page File
     */
    public function setPage(string $page) {
        $this->page = $page;
    }
    public function getPage():string {
        return $this->page;
    }

    public function renderPage() {
        $page = new File($this->page);
        $page->clearSearchFolders();
        $page->addSearchFolder($this->getTemplateDir());
        if($page->hasFile()) {
            include($page->getFilePath());
        } else {
            throw new \Exception("Page not found: ".$this->page. " in dir: ".$this->getTemplateDir());
        }
    }
}
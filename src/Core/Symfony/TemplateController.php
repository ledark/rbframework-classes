<?php 

namespace RBFrameworks\Core\Symfony;

use Symfony\Component\HttpFoundation\Response;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Utils\Replace;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class TemplateController {

    public $page = "";

    private static function getTemplateFile():string {
        return Config::get('symfony.template_dir').Config::get('symfony.template_filename');
    }

    public function getTemplateFileContent(array $injectVariables = []):string {
        ob_start();
        include(self::getTemplateFile());
        return Replace::replace(ob_get_clean(), $injectVariables);
    }
    
    public function render(string $page, $data = [], $config = []) {
        if(!count($data)) $data = [
            'page' => $page,
        ];
        if(!isset($config['cachePage'])) $config['cachePage'] = 0;
        $this->setPage($page);

        $cache = new FilesystemAdapter(); 
        $content = $cache->get($page.md5(serialize($data)), function (ItemInterface $item) use ($data, $config) {
            $item->expiresAfter($config['cachePage']);
            return $this->getTemplateFileContent($data);
        });
        return new Response($content);
    }

    public function setPage(string $page) {
        $this->page = $page;
    }

    public function renderPage():void {
        $page = new File($this->page);
        $page->addSearchFolder(Config::get('symfony.template_dir'));
        if($page->hasFile()) {
            include($page->getFilePath());
        } else {
            throw new \Exception("Page not found: ".$this->page);
        }
    }
}
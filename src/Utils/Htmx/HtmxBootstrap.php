<?php 

namespace Framework\Utils\Htmx;

use Framework\Config;

/**
 * Todas as configurações do Htmx estão nessa classe
 */
class HtmxBootstrap {

    public array $config;

    public function __construct() {

        $this->config = [

            /** Onde os componentes são encontrados?
             * Defina searchFolders e searchFiles.
             * Quando um componente é encontrado, ele procura nos namespaces definidos em searchNamespaces.
             * O nome da classe do componente deve ser o mesmo que o nome do arquivo.
            */
            'searchFolders' => [
                __DIR__.'/Components/', //@todo carregar from collection
            ],
            'searchFiles' => [
                '[name]',
                '[name].php',
            ],
            'searchNamespaces' => [
                'Framework\\Utils\\Htmx\\Components\\',
            ],

            'mode' => Constants\Mode::DEBUG,
            'route' => '{httpSite}htmx',
            'script_src' => '{httpSite}front/startbootstrap-sb-admin-gh-pages/js/htmx.min.js', //@todo mannter arquivo local

        ];

        $this->config['searchFolders'] = array_merge($this->config['searchFolders'], Config::get('htmx.searchFolders', []));
        $this->config['searchFiles'] = array_merge($this->config['searchFiles'], Config::get('htmx.searchFiles', []));
        $this->config['searchNamespaces'] = array_merge($this->config['searchNamespaces'], Config::get('htmx.searchNamespaces', []));

        $this->config['mode'] = Config::get('htmx.mode', $this->config['mode']);
        $this->config['route'] = Config::get('htmx.route', $this->config['route']);
        $this->config['script_src'] = Config::get('htmx.script_src', $this->config['script_src']);

    }





    public static function getConfig():array {
        return (new self())->config;
    }

    public static function getRoute():string {
        $route = (new self())->config['route'];
        $route = str_replace('{httpSite}', Config::get('server.base_uri'), $route);
        return $route;
    }

    public static function getScriptSrc(bool $withTags = true):string {
        $src = (new self())->config['script_src'];
        $src = str_replace('{httpSite}', Config::get('server.base_uri'), $src);
        return $withTags ? '<script src="'.$src.'"></script>' : $src;
    }

}

<?php

namespace RBFrameworks\Core\Assets;

use RBFrameworks\Core\Http;
use RBFrameworks\Core\Legacy\SmartReplace;
use RBFrameworks\Core\Directory;
use RBFrameworks\Core\Config;

class StreamFile {

    public $realfilepath;
    public $replaces = [];
    public $cache = false;
    public $filename = '';

    private $fakepath = '';

    /**
     * (new StreamFile('file.txt'))->getFilepath(); //faz a copia e retorna o fakepath copiado
     *
     * @param string $realfilepath
     * @param array $replaces
     */
    public function __construct(string $realfilepath, array $replaces = []) {
        if(!file_exists($realfilepath)) {
            throw new \Exception('File not found in Assets\StreamFile: '.$realfilepath);
        }
        $this->realfilepath = $realfilepath;
        $this->replaces = $replaces;
    }

    private static function getCacheAssetsFolder():string {
        $cache_assets = Config::get('location.cache.assets');
        if(is_null($cache_assets)) throw new \Exception('Config location.cache.assets not found');
        return Directory::rtrim($cache_assets);
    }

    private function getExtension():string {
        return '.'.pathinfo($this->realfilepath, PATHINFO_EXTENSION);
    }

    private function mkdir(string $path) {
        if(strpos($path, '/') !== false) {
            $parts = explode('/', $path);
            array_pop($parts);
            Directory::mkdir(implode('/', $parts));
        }
    }

    private function getFakepath():string {
        if(!empty($this->fakepath)) return $this->fakepath;
        $overname = $this->filename;
        $cache_assets = self::getCacheAssetsFolder();
        if(!empty($overname)) {
            $fakepath = $cache_assets.'/fnfiles_'.$overname.$this->getExtension();
            if(strpos($overname.$this->getExtension(), '.js.js')) {
                $fakepath = $cache_assets.'/fnfiles_'.$overname;
            }
        } else {
            $fakepath = $cache_assets.'/fnfiles_'.md5($this->realfilepath).$this->getExtension();
        }
        
        //Genera se Nao Existir
        if(!file_exists($fakepath)) {
            $this->mkdir($fakepath);
            copy($this->realfilepath, $fakepath);
        }

        //Regera se Tamanho for Diferente
        if (filesize($fakepath) != filesize($this->realfilepath)) {
            copy($this->realfilepath, $fakepath);
        }
        $this->fakepath = $fakepath;
        return $fakepath;
    }

    private function process() {
        if(count($this->replaces)) {
            $content = file_get_contents($this->realfilepath);
            foreach($this->replaces as $key => $value) {
                $content = str_replace('{'.$key.'}', $value, $content);
            }
            file_put_contents($this->getFakepath(), $content);
        }
    }

    public function getFilePath():string {
        $this->process();
        return $this->getFakepath();
    }

    public function getHttpPath():string {
        $this->process();
        return Http::getSite().$this->getFakepath();
    }



}
<?php

namespace RBFrameworks\Helpers;

use RBFrameworks\Helpers\Angularv1 as Angular;

if(!defined('HTTPSITE')) define('HTTPSITE', (new \RBFrameworks\Request\Request())->getHttpSite() );

class Angularv2 {
    
    private $collection;
    public $config = [];
    public $setup = '';
    private $COLLECTION_HANDLE = 'memory';
    
    
    private function checkDependences(): object {
        if(!class_exists('\RBFrameworks\Helpers\Collections')) throw new \Exception("Dependencia Collections necessaria");
        
        $this->collection = new \RBFrameworks\Helpers\Collections('angular', '', $this->COLLECTION_HANDLE);

        plugin("dispatcher");
        
        return $this;
    }
    
    public function __construct(string $handle = 'memory') {
        $this->COLLECTION_HANDLE = $handle;
        $this->checkDependences();
    }
    
    /**
     * Voc� chama um setup com todas as configura��es necess�rias para esse angular, e nada mais precisa ser configurado.
     * Todas as configura��es dentro do collection s�o opcionais, e os detalhes est�o na pr�pria collection
     * @param string nome do setup que est� na collection angular.setup
     * @return object $this
     */
    public function useSetup($setup): object {
        if(is_string($setup)) {   
            $this->setup = $setup;
            $this->config = $this->collection->get('setup.'.$setup);
        } else
        if(is_array($setup)) {
            $this->config = $setup;
        }
        return $this;
    }
    
    public function addConfig(string $name, $value):object {
        if(!isset($this->config[$name])) throw new \Exception("Configuracao do Angularv2 $name nao encontrada.");
        return $this;
    }
    
    public function setConfig(string $name, $value):object {
        if(strpos($name, '.') !== false) {
            $namedoted = explode('.', $name);
            $configname = array_shift($namedoted);
            if(!isset($this->config[$configname])) throw new \Exception("Configuracao do Angularv2 {$configname} nao encontrada.");
            $toeval = "\$this->config['{$configname}']['".implode("']['", $namedoted)."'] = \$value;";
            eval($toeval);
        } else {
            $this->config[$name] = $value;
        }
        return $this;
    }
    
    public function change(string $varName, $newValue):object {
        
        $this->collection->set('setup.'.$this->setup.'.'.$varName, $newValue);

        /*
        (new \Helpers\HandleCollections('angular'))->set('setup.'.$this->setup.'.'.$varName, $newValue);
        */
        return $this;
    }
    
    public function run():string {
        $Angular = new Angular($this->config['ng-controller'] ?? 'Conteudo', $this->config['ng-app'] ?? 'mainApp');
        foreach($this->config as $chave => $valor) {
            $this->handleConfig($chave, $valor, $Angular);
        }
        return $Angular->render();
    }
    
    public function handleConfig(string $configName, $value, Angular &$AngularObject) {
        $configName = dispatcher_camelcased($configName);
        $method = "handleConfig{$configName}";
        try {
            new \ReflectionMethod($this, $method);
            $this->$method($value, $AngularObject);
        } catch(\ReflectionException $e) {
            
        }
    }
    
    protected function handleConfigModules($value, Angular &$AngularObject) {
        $string = '';
        if(is_array($value)) {
            foreach($value as $angular_module) {
                if(strpos($angular_module, '$') !== false) {
                    $string.= $angular_module.', ';
                } else {
                    $string.= "'".$angular_module.'\', ';
                }
            }
            $string = rtrim($string, ', ');
        }
        if(!empty($string)) $AngularObject->module($string);
    }
    
    protected function handleConfigScripts($value, Angular &$AngularObject ) {
        foreach($value as $v) {
            $AngularObject->script($v);
        }
    }
    
    protected function handleConfigInterpolate($value, Angular &$AngularObject ) {
        $AngularObject->interpolate($value[0], $value[1]);
    }
    
    protected function handleConfigLoadControllers($value, Angular &$AngularObject ) {
        $AngularObject->loadControllers($value);
    }
    
    protected function handleConfigRequires($value, Angular &$AngularObject ) {
        foreach($value as $v) {
            $AngularObject->required($v);
        }
    }
    
    protected function handleConfigLoad($value, Angular &$AngularObject ) {
        $AngularObject->load($value[0], $value[1], $value[2]);
    }
    
    protected function handleConfigReplaces($value, Angular &$AngularObject ) {
        foreach($value as $a => $b) {
            $AngularObject->replace($a, $b);
        }
    }
    
    protected function handleConfigScopeVars($value, Angular &$AngularObject ) {
        foreach($value as $a => $b) {
            $AngularObject->setScopeVar($a, $b);
        }
    }
    
    protected function handleConfigFilters($value, Angular &$AngularObject) {
        foreach($value as $index => $config) {
            $filterName = key($config);
            $filterValue = $config[$filterName];
            $AngularObject->addFilter($filterName, $filterValue);
        }
    }
    
}

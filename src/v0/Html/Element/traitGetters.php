<?php

namespace RBFrameworks\Html\Element;

trait traitGetters {
    private function getName():string {
        return $this->name;
    }
    public function getAttr(string $name = ''):string {
        return (empty($name)) ? $this->array2attributes($this->attr) : $this->attr[$name];
    }
    private function getValue():string {
        $this->setValue(smart_replace($this->value, $this->replaces, true));
        return $this->value;
    }
    

    public function getInstance() {
        return $this->value;
    }
    
    private function getBefore():string {
        return $this->before;
    }
    private function getAfter():string {
        return $this->after;
    }
    
}

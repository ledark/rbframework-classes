<?php

namespace RBFrameworks\Html\Element;

trait traitRenderers {
    public function render():string {
        
        return $this->getBefore()."<{$this->getName()}{$this->getAttr()}>{$this->getValue()}</{$this->getName()}>".$this->getAfter();
    }
    public function renderOutput() {
        echo $this->render();
    }  

    public function __toString() {
        return $this->render();
    }
    
}

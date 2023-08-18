<?php

namespace RBFrameworks\Html\Element;

trait traitSetters {
    
    public function generateID():object {
        $this->id = (isset($this->attr['id'])) ? $this->attr['id'] : $this->getName().uniqid('_');
        return $this;
    }
    
    public function setName(string $name):object {
        $this->name = $name;
        return $this;
    }
    
    public function setValue($mixed): object {
        $this->value = $mixed;
        return $this;
    }
    
    public function setAttr(array $attr):object {
        $this->attr = array_merge($this->attr, $attr);
        return $this;
    }
    
    /**
     * Associe variáveis ao conteúdo, usando como base o smart_replace para variáveis do tipo {chave}
     * @param array $variables
     * @return object
     */    
    public function setReplaces(array $replaces):object {
        $this->replaces = array_merge($this->replaces, $replaces);
        return $this;
    }
    
    //Alias para setReplaces
    public function assign(array $variables): object {
        $this->setReplaces($variables);
        return $this;
    }
}

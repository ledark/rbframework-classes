<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace RBFrameworks\Html;

/**
 * Description of Element
 *
 * @author Lenovo
 */
class Element {
    
    protected $id = null;
    private $name = '';
    private $attr = [];
    private $value = null;
    private $replaces = [];
    
    private $before = '';
    private $after = '';


    use Element\traitGetters;
    use Element\traitSetters;
    use Element\traitRenderers;

    public function __construct(string $element, array $attr = [], $value = '') {

        $this->setName($element);
        $this->setAttr($attr);
        $this->setValue($value);
        $this->generateID();
        
        return $this;
    }
    
    private function array2attributes(array $array):string {
        $attr = ' ';
        foreach($array as $name => $value) {
            $attr.= "$name=\"$value\" ";
        }
        return $attr;
    }
    
    public function append(Element $Element) {
        $this->setValue($Element);
        return $this;
    }
    

    public function insertBefore(string $content) {
        $this->before = $content;
        return $this;
    }
    public function insertAfter(string $content) {
        $this->after = $content;
        return $this;
    }
    
    
}

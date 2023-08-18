<?php

namespace RBFrameworks\Html\Element;

class div extends \RBFrameworks\Html\Element {
    
    public function __construct(array $attr = [], $value = '') {
        $element = 'div';
        $attr = [
            'id' => uniqid('a'),
        ];
        $this->setAttr($attr);
        parent::__construct($element, $attr, $value);
    }
    
}

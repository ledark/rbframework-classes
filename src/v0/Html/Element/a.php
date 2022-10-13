<?php

namespace RBFrameworks\Html\Element;

class a extends \RBFrameworks\Html\Element {
    
    public function __construct(string $href, $content = null) {
        $element = 'a';
        $attr = [
            'id' => uniqid('a'),
            'href' => $href
        ];
        $value = $content ?? $href;
        parent::__construct($element, $attr, $value);
    }
    
}

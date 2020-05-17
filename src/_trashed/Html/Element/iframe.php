<?php

namespace RBFrameworks\Html\Element;

class iframe extends \RBFrameworks\Html\Element {
    
    public function __construct(string $src, string $pushstate = '') {
        $element = 'iframe';
        $attr = [
            'id' => uniqid('iframe'),
            'src' => $src,
            'name' => md5($src),
            'scrolling' => 'no',
            'importance' => 'high',
            'style' => 'overflow:hidden; border:none; width: 100%; height: 100vh;',
        ];
        $javascript = new \RBFrameworks\Html\Javascript(file_get_contents(__DIR__.'/iframeresizes.js'));
        $javascript->setReplaces($attr);
        $this->insertAfter($javascript);
        parent::__construct($element, $attr, '');
    }
    
}

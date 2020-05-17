<?php

namespace RBFrameworks\Html\Element\Bootstrap;

class ProgressBar extends \RBFrameworks\Html\Element {
    
    public function __construct(string $streamuri, string $text = 'Inicializando...') {

        $attr = [
            'id' => 'progress'. uniqid(),
            'class' => 'progress-bar progress-bar-striped progress-bar-animated',
            'aria-valuenow'=>"75" ,
            'aria-valuemin'=>"0" ,
            'aria-valuemax'=>"100" ,
            'style'=>"width: 75%",
            'height' => '25',
            ];
        
        $progress = new \RBFrameworks\Html\Element('div', $attr, '');
        
        $javascript = new \RBFrameworks\Html\Javascript(file_get_contents(__DIR__.'/ProgressBar.js'));
        $javascript->setReplaces($attr);
        $javascript->setReplaces(['httpStream' => $streamuri]); 
        $this->insertAfter((new \RBFrameworks\Html\Element('div', ['class' => $attr['id']], $text)).$javascript);        
        
        parent::__construct('div', ['class' => 'progress'], $progress);
        
    }
    
    public function setProgress(int $value) {
        $id = $this
            ->getInstance()
            ->setAttr(['style' => 'width:'.$value.'%', 'aria-valuenow'=>"$value"])
            ->getAttr('id')
        ;
        
        return $this;
    }
    


}



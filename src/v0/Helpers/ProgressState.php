<?php

namespace RBFrameworks\Helpers;

class ProgressState extends \RBFrameworks\Database\Utils\Pairs {
    
    public $state = [
        'min'   => 0,
        'now'   => 0,
        'max'   => 0,
        'txt'   => '',
    ];
    
    public function __construct(string $id) {
        parent::__construct('rbf_progress_state');
        
        $this->setVarname($id);
        $this->state = $this->get();
        
    }
    
    public function state() {
        return $this->state;
    }
    
    public function reset(int $min = 0, int $now = 0, int $max = 100, string $txt = "") {
        return $this
            ->min($min)
            ->now($now)
            ->max($max)
            ->txt($txt)
        ;
    }
    
    public function min(int $value): object {
        $this->state['min'] = $value;
        return $this;
    }
    
    public function now(int $value): object {
        $this->state['now'] = $value;
        return $this;
    }
    
    public function max(int $value): object {
        $this->state['max'] = $value;
        return $this;
    }
    
    public function txt(string $value): object {
        $this->state['txt'] = $value;
        return $this;
    }

    public function save() {
        $this->set($this->state);
    }
    
    public function __destruct() {
        $this->save();
    }
    
}


<?php

namespace RBFrameworks\Interfaces;

interface Renderizable {
    
    public function render(): string;
    
    public function display(): void;
    
}

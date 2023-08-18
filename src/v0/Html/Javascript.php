<?php

namespace RBFrameworks\Html;

class Javascript {
    
    use Element\traitSetters;
    
    private $type = 'inline';
    private $content = '';
    private $replaces = [];
    
    public function __construct(string $constructor) {
        if(file_exists($constructor)) {
            $this->type = 'script';
            $this->content = $constructor;
        } else {
            $this->type = 'inline';
            $this->content = $constructor;
        }
    }
    
    public function render() {
        if($this->type == 'script') {
            $FileHandler = new \RBFrameworks\Helpers\Files();
            $filepath = $FileHandler->getSafePath($this->content);
            return $filepath.'<script src="'.$filepath.'"></script>';
        } else
        if($this->type == 'inline') {
            return '<script>'. smart_replace($this->content, $this->replaces, true).'</script>';
        }
    }
    
    public function __toString() {
        return $this->render();
    }
    
}

<?php

namespace RBFrameworks\Core\Templates;

use Core\Template;
use Core\Utils\FileReplace;

class Tmpl {

    public $tmpl;
   
    public function __construct(string $tmpl) {
        $this->tmpl = $tmpl;
        $this->file = new FileReplace($this->tmpl);
        //$this->file->addSearchFolders( debug );
        $this->file->addSearchExtension('.html');

    }

    public function render() {
        echo $this->file->render();
    }

}
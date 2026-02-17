<?php

namespace RBFrameworks\Core\Database;

use RBFrameworks\Core\Utils\Strings\Dispatcher;
use RBFrameworks\Core\Legacy\Template;

class Form {
    
    public $tmplDir = __DIR__.'/Form/Templates/Bootstrap5/';
    public $model = [];

    public function __construct(array $model) {
        $this->model = $model;
    }

    /*
    private function generateField(array $prop) {
        $content = Template::usar($this->tmplDir.'Input.html');
    }

    public function generate() {
        foreach($this->model as $field => $prop) {
            $prop['field'] = $field;
            $prop['label'] = isset($prop['label']) ? $prop['label'] : Dispatcher::label($field);
            $this->generateField($prop);
        }
    }
    */

}
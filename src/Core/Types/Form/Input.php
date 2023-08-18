<?php 

namespace RBFrameworks\Core\Types\Form;

use RBFrameworks\Core\Types\HtmlElement;
use RBFrameworks\Core\Types\Form\FormInterface;

class Input extends HtmlElement implements FormInterface {

    public function __construct(string $name) {
        parent::__construct(null, 'input', [
            'id' => $name,
            'name' => $name,
            'type' => 'text',
        ]);
    }

}
<?php

namespace RBFrameworks\Core\Response\Mock;

use RBFrameworks\Core\Data;

class Schema extends Data {

    public function __construct(array $dados = []) {
        parent::__construct($dados, self::ARRAY_AS_PROPS); //or STD_PROP_LIST
    }

}
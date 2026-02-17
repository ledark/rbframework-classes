<?php

namespace RBFrameworks\Core\Types\Sql;

class Common
{
    private $originalValue = null;

    public function __construct($mixedValue) {
        $this->originalValue = $mixedValue;
    }

    public function getOriginalValue() {
        return $this->originalValue;
    }
    
}

<?php

namespace RBFrameworks\Core\Types\Sql;

class Number extends Common
{

    public function getQueryConstructor():string {
        return $this->getOriginalValue();
    }
}

<?php

namespace RBFrameworks\Core\Validator;

class Rule {

    public function setValidState(bool $valid) {
        $this->_valid = $valid;
    }

    public function isValid():bool {
        return isset($this->_valid) and is_bool($this->_valid) ? $this->_valid : false;
    }
}

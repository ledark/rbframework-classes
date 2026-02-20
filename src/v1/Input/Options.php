<?php

namespace Framework\Input;

class Options {

    public mixed $default;
    public bool $getFromAnywhere;
    public bool $decodeUTF8;
    public bool $sanitize;

    public function __construct() {
        $this->default = '';
        $this->getFromAnywhere = true;
        $this->decodeUTF8 = true;
        $this->sanitize = true;
    }

}
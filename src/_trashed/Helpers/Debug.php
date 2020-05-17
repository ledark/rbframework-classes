<?php

namespace RBFrameworks\Helpers;

abstract class Debug {
    
    public static function pre($mixed) {
        echo '<pre>';
        print_r($mixed);
        echo '</pre>';
    }
    
}

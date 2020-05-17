<?php 

namespace RBFrameworks\Component;

interface ComponentInterface {
    
    public const code = '<?php ?>';

    public function getSchema():array;
    
}
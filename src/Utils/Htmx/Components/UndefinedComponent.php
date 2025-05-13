<?php 

namespace Framework\Utils\Htmx\Components;

class UndefinedComponent extends \Framework\Utils\Htmx\HtmxComponent {

    public function __construct() {
        parent::__construct('<div style="display: inline-block; font-size: 90%; background: #DFF; padding: 0.5em;" >Nenhum component definido</div>');
    }

}
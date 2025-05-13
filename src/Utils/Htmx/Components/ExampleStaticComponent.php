<?php 

namespace Framework\Utils\Htmx\Components;

class ExampleStaticComponent extends \Framework\Utils\Htmx\HtmxComponent {

    public function __construct() {
        parent::__construct('<div style="display: inline-block; font-size: 120%; background: #DFF; padding: 1em;" >Exemplo de Componente Estático: '.get_class($this).'</div>');
    }

}
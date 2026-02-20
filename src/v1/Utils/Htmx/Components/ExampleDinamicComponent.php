<?php 

namespace Framework\Utils\Htmx\Components;

use Framework\Utils\Htmx\Helper\Component;
use Framework\Utils\Htmx\HtmxBootstrap;

class ExampleDinamicComponent extends \Framework\Utils\Htmx\HtmxComponent {

    public function __construct() {
        parent::__construct();
        $html = '
        <div'.
            Component::attr($this).
            Component::attr($this, 'hx-trigger', 'every 1s').
            Component::attr($this, 'hx-target', 'body').
            '
            style="display: inline-block; font-size: 120%; background: #DFF; padding: 1em;">Exemplo de Componente Dinmâmico: '.date('Y-m-d H:i:s').'</div>';
        $this->html = Component::wrapped($html);
    }

}
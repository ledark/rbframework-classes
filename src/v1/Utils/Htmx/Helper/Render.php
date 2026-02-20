<?php 

namespace Framework\Utils\Htmx\Helper;

use Framework\Utils\Htmx\HtmxBootstrap;
use Framework\Utils\Htmx\HtmxComponent;
use Framework\Utils\Htmx\Constants\Mode;

class Render {

    public static function error(string $message):HtmxComponent {
        $html = (HtmxBootstrap::getConfig()['mode'] == Mode::DEBUG) ? '<div data-error-control="htmx-error" style="

    background: #b1926a;
    color: #FFF;
    display: inline-block;
    padding: 0.5em;
    border: 2px solid #F00;
    border-radius: 0.25em;
"
        >HtmxError: '.$message.'</div>' : '';
        return new HtmxComponent($html, 'error');
    }

}
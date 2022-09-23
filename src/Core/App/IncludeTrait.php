<?php

namespace RBFrameworks\Core\App;

trait IncludeTrait
{

    private function includePagePartPhp(string $partName = ''):bool {
        $file2include = $this->getPageComponent('.'.$partName, '.php');
        if(file_exists($file2include)) {
            include($file2include);
            return true;
        }
        return false;
    }

    private function includePagePartHtml(string $partName = ''):bool {
        $file2include = $this->getPageComponent('.'.$partName, '.html');
        if(file_exists($file2include)) {
            include($file2include);
            return true;
        }
        return false;
    }

    private function includePagePartCss(string $partName = 'css'):bool {
        $file2include = $this->getPageComponent('.'.$partName, '');
        if(file_exists($file2include)) {
            echo '<style type="text/css">';
            include($file2include);
            echo '</style>';
            return true;
        }
        return false;
    }

    private function includePagePartJs(string $partName = 'js'):bool {
        $file2include = $this->getPageComponent('.'.$partName, '');
        if(file_exists($file2include)) {
            echo '<script type="text/javascript">';
            include($file2include);
            echo '</script>';
            return true;
        }
        return false;
    }

    private function includePagePartAuto(string $partName = ''):bool {
        $file2include = $this->getPageComponent('.'.$partName);
        if(file_exists($file2include)) {
            include($file2include);
            return true;
        }
        return false;
    }
}

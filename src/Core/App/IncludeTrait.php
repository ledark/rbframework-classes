<?php

/*
Copyright (c) 2022 Ricardo Bermejo
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. Neither the name of copyright holders nor the names of its
   contributors may be used to endorse or promote products derived
   from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL COPYRIGHT HOLDERS OR CONTRIBUTORS
BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * @author   "Ricardo Bermejo" <ricardo@bermejo.com.br>
 * @package  App
 * @version  1.0
 * @license  Revised BSD
  */
  
namespace RBFrameworks\Core\App;

trait IncludeTrait
{
    private function includeRootPartPhp(string $rootpartname):bool {
        $file2include = rtrim($this->page->getFolderPath(), '/').'/'.$rootpartname.'.php';
        if(file_exists($file2include)) {
            include($file2include);
            return true;
        }
        return false;
    }

    private function getPageComponent(string $subExtension = '', string $finalExtension = null):string {
        $baseDir = rtrim($this->page->getFolderPath(), '/');
        $fileName = ltrim($this->page->getName(), '/');
        $extension = is_null($finalExtension) ? $this->page->getExtension() : $finalExtension;
        return $baseDir.'/'.$fileName.$subExtension.$extension;
    }

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

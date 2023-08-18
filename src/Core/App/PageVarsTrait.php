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

use RBFrameworks\Core\Utils\Arrays;

use RBFrameworks\Core\Exceptions\AppException as Exception;

trait PageVarsTrait {

    public function addPageVar(string $key, string $value): object {
        $this->pageVars[$key] = $value;
        return $this;
    }

    public function addPageVars(array $pageVars, bool $overwrite = false): object {
        return $this->setPageVars($pageVars, $overwrite);
    }


    public function setPageVars(array $pageVars, bool $overwrite = true): object {
        foreach($pageVars as $key => $value) {
            if(isset($this->pageVars[$key]) and $overwrite == false) {
                continue;
            }
            if(is_string($value)) {
                $this->addPageVar($key, $value);
                continue;
            }
            if(is_array($value) and !Arrays::isAssoc($value)) {
                $this->addPageVar($key, implode(', ', $value));
                continue;
            }
            if(is_array($value) and Arrays::isAssoc($value)) {
                $newPageVars = [];
                foreach($value as $k => $v) {
                    $newPageVars[$key.'.'.$k] = $v;
                }
                $this->setPageVars($newPageVars, $overwrite);
            }
        }
        return $this;
    }

    public function getPageVars(): array {
        return $this->pageVars;
    }

    public function getPageVar(string $key): string {
         return isset($this->pageVars[$key]) ? $this->pageVars[$key] : "";
    }

    public function renderPageVar(string $key): void {
        echo $this->getPageVar($key);
    }
}
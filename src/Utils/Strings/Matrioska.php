<?php
/*
Copyright (c) 2021 Ricardo Bermejo
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
 * @package  Matrioska
 * @version  1.0
 * @license  Revised BSD
 * 
 * Transforme Strings em Conteúdos Encapsulados.
 * 
 **/

namespace Framework\Utils\Strings;

class Matrioska
{

    private $content = "";

    public function addLeft(string $l): object
    {
        $this->content = $l . $this->content;
        return $this;
    }

    public function addRight(string $r): object
    {
        $this->content = $this->content . $r;
        return $this;
    }

    public function replace(string $name, string $content): object
    {
        $this->content = str_replace($name, $content, $this->content);
        return $this;
    }

    public function addCapsule(string $l = "", string $r = ""): object
    {
        $this->add($l, $r);
        return $this;
    }

    //adiciona conteudo a Left e Right do Conteúdo Atual, encapsulando-o
    public function add(string $l = "", string $r = ""): object
    {
        $this->content = $l . $this->content . $r;
        return $this;
    }

    //adiciona conteúdo
    public function get(): string
    {
        return $this->content;
    }

    public function __toString(): string
    {
        return $this->get();
    }
}

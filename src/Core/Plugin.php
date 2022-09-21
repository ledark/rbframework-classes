<?php
/*
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
 * @copyright Copyright (c) 2021 Ricardo Bermejo
 * @package  Core\Plugin
 * @version  1.0.0 [Core v1.98.2] Ago/2021
 * @license  Revised BSD
 */

namespace RBFrameworks\Core;

class Plugin
{

    public static function load($functionname)
    {
        if (!function_exists($functionname)) {

            $functionname = str_replace('\\', '/', $functionname);

            $include_class_paths = [
                __DIR__."/../../../_app/functions/{$functionname}.php",
                __DIR__."/../../../_app/functions/function.{$functionname}.php",
                __DIR__."/../../../_app/functions/legacy/function.{$functionname}.php",
            ];

            if (strpos($functionname, '_') !== false) {
                $arr = explode('_', $functionname);
                $include_class_paths[] =  "_app/functions/{$arr[0]}.php";
            }

            foreach ($include_class_paths as $inc) {
                if (file_exists($inc)) {
                    include($inc);

                    return;
                }
            }

            throw new \Exception("fn $functionname not founded in [" . implode('], [', $include_class_paths) . ']');
        }
    }

    public static function __callStatic($name, $arguments = null)
    {
        self::load($name);
        call_user_func_array($name, $arguments);
    }
}

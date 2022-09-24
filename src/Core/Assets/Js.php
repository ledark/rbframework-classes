<?php

namespace RBFrameworks\Core\Assets;

class Js
{

    // JavaScript Minifier
    public static function minify_js($input, array $flags = ['comment', 'whitespace']) {
        //return $input;
        if(trim($input) === "") return $input;

        $remove_commentr = '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#';
        $remove_commenti = '$1';
        $remove_whitespacer = '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s';
        $remove_whitespacei = '$1$2';
        $remove_lastsemicolonr = '#;+\}#';
        $remove_lastsemicoloni = '}';
        $minify_object_attrr = '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i';
        $minify_object_attri = '$1$3';
        $ibidr = '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i';
        $ibidi = '$1.$3';
            return preg_replace(
            [
                $remove_commentr, $remove_lastsemicolonr, $minify_object_attrr, $ibidr
            ], [
                $remove_commenti, $remove_lastsemicoloni, $minify_object_attri, $ibidi
            ], $input
        );



        return preg_replace(
            array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
            ),
            array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3'
            ),
        $input);
    }


}

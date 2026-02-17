<?php

namespace RBFrameworks\Core\Templates;

use Core\Template;


class Bootstrapv5
{
    private static function renderTmpl(string $filename, array $replaces = [], string $chunk = null):string{
        $fileTmpl = __DIR__.'/Bootstrapv5/'.$filename.'.tmpl';
        //$content = file_get_contents($fileTmpl);
        //return $content;
        return Template::usar($fileTmpl, $replaces, $chunk);
    }
    public static function navbar():string {
        $content = self::renderTmpl('navbar');
        return $content;
    }
    public static function navbarHeader(array $replaces = []):string {
        $content = self::renderTmpl('navbar', $replaces, 'header');
        return $content;
    }
    public static function navbarItem(array $replaces = []):string {
        $content = self::renderTmpl('navbar', $replaces, 'item');
        return $content;
    }
    public static function navbarFooter(array $replaces = []):string {
        $content = self::renderTmpl('navbar', $replaces, 'footer');
        return $content;
    }
    
}

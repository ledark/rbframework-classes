<?php 

namespace Framework\Utils\Htmx\Helper;

use Framework\Utils\Htmx\HtmxBootstrap;
use Framework\Utils\Htmx\HtmxComponent;
use Framework\Utils\Htmx\Constants\Mode;

class Strings {

    public static function toWordsCase(string $string, array $delimiters = ['_', '-', '\\', '/', '|']):string {
        foreach($delimiters as $delimiter) {
            $string = str_replace($delimiter, ' ', $string);
        }
        return $string;
    }

    public static function toCamelCase(string $string):string {
        $string = str_replace(' ', '', lcfirst(ucwords(self::toWordsCase($string, ['-', '_', '|']))));
        return $string;
    }

    public static function toKebabCase(string $string):string {
        $string = str_replace(' ', '-', self::toWordsCase($string));
        $string = preg_replace('/([A-Z])/', '-$1', $string);
        return strtolower($string);
    }

    public static function toSnakeCase(string $string):string {
        $string = str_replace(' ', '_', self::toWordsCase($string));
        $string = preg_replace('/([A-Z])/', '_$1', $string);
        return strtolower($string);
    }

    public static function toPascalCase(string $string):string {
        return str_replace(' ', '', ucwords(self::toWordsCase($string, ['-', '_', '|'])));
    }

}
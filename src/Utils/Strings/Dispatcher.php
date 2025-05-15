<?php 

namespace Framework\Utils\Strings;

abstract class Dispatcher {
    
    public static function camelcased2sef(string $string):string {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
          $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        $converted = implode('_', $ret);        
        return self::sef($converted);
    }

    public static function sef($string) {
        $palavra = strtr($string, "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ", "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
        $palavranova = str_replace("_", " ", $palavra);
        $pattern = '|[^a-zA-Z0-9\-\.]|';
        $palavranova = preg_replace($pattern, ' ', $palavranova);
        $string = str_replace(' ', '-', $palavranova);
        $string = str_replace('.', '-', $string);
        $string = str_replace('---', '-', $string);
        $string = str_replace('--', '-', $string);
        return strtolower($string);
    }

    public static function camelcased($string, $ignore_first = false) {
        $newString = "";
        $string = self::sef($string);
        $string = ucwords($string);
        $string = explode('-', $string);
        $count = 0;
        foreach ($string as $s) {
            $count++;
            if($ignore_first and $count == 1) {
                $newString .= $s;
            } else {
            $newString .= ucfirst($s);
            }
        }
        return $newString;
    }

    public static function underscored($string) {
        $string = self::sef($string);
        $pattern = '|([A-Z])|';
        $string = preg_replace($pattern, '_$1', $string);
        $string = explode('-', $string);
        $newString = "";
        foreach ($string as $s)
            $newString .= $s . '_';
        $string = strtolower($newString);
        $string = str_replace('___', '_', $string);
        $string = str_replace('__', '_', $string);
        $string = str_replace('.', '_', $string);
        $string = trim($string, '_');
        return $string;
    }
    
    public static function file($string) {
        $palavra = strtr($string, "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ", "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
        $palavranova = str_replace("_", " ", $palavra);
        $pattern = '|[^a-zA-Z0-9\-\.]|';
        $palavranova = preg_replace($pattern, ' ', $palavranova);
        $string = str_replace(' ', '-', $palavranova);
        $string = str_replace('---', '-', $string);
        $string = str_replace('--', '-', $string);
        $string = strtolower($string);

        $pattern = '|([A-Z])|';
        $string = preg_replace($pattern, '_$1', $string);
        $string = explode('-', $string);
        $newString = "";
        foreach ($string as $s)
            $newString .= $s . '_';
        $string = strtolower($newString);
        $string = str_replace('___', '_', $string);
        $string = str_replace('__', '_', $string);
        $string = trim($string, '_');
        return $string;
    }
    
    public static function label(string $string):string {        
        $string = str_replace(' ', '-', $string);
        $string = str_replace('.', '-', $string);
        $string = str_replace('---', '-', $string);
        $string = str_replace('--', '-', $string);
        $string = str_replace('-', ' ', $string);
        $string = ucwords($string);
        $stringParts = explode(' ', $string);
        $words = [];
        foreach($stringParts as $word) {
            if(in_array($word, ['da', 'de', 'do', 'ao', 'as', 'is', 'para', 'em', 'ou'])) {
                $word = strtolower($word);
            }
            $words[] = $word;
        }
        return implode(' ', $words);
    }

}
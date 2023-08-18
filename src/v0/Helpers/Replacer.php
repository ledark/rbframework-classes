<?php

namespace RBFrameworks\Helpers;

class Replacer {
    
    /**
     * ShortTags capturam <br/> <p/> e <qualquer/>
     * @var type 
     */
    public $shortTags = '/<([\w\s]+)\s?\/>/mi';
    
    public $elementaryTags = '';
    
    public static function regex_getTag(string $tagname) {
        return '/<\s?'.$tagname.'\s?>(.*)<\s?\/\s?'.$tagname.'\s?>/mi';
    }
    
    public static function replaceTagContent(string $str, string $tagname = 'php', string $subst = '<?php echo $1 ?>') {
        $re = self::regex_getTag($tagname);
        return preg_replace($re, $subst, $str);
    }
    
    public static function smartReplace(string $str, array $replaces) {
        return smart_replace($str, $replaces, true);
    }
}

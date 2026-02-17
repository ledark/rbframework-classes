<?php

namespace RBFrameworks\Core\Assets;

use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Assets;

class Css
{

    public static function getTag(string $uri):string {
        return '<link rel="stylesheet" href="'.$uri.'">';
    }

    public static function getTagNormal(string $uri):string {
        return self::getTag($uri);
    }

    /**
     * includeFile(string $file)
     *  - include a css file in inline style
     * @param string $file
     * @return void
     */
    public static function includeFile(string $file) {
        $FileObject = new File($file);
        $FileObject->clearSearchExtensions();
        $FileObject->addSearchExtensions(['', '.css']);
        if($FileObject->hasFile() and $FileObject->getExtension() == '.css') {
            ob_start();
            echo "\r\n";
            echo "<!-- AssetsCss:[". basename($file, '.css') ."] -->\r\n";
            echo '<style type="text/css">';
            include($FileObject->getFilePath());
            echo '</style>';
            echo "\r\n";
            $content = ob_get_clean();
        }
        if(isset($content)) {
            Assets::Render('footer', $content);
        }
    }

    // CSS Minifier => http://ideone.com/Q5USEF + improvement(s)
    public static function minify_css($input)
    {
        if (trim($input) === "") {
            return $input;
        }
        return preg_replace(
            array(
                    // Remove comment(s)
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                    // Remove unused white-space(s)
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                    // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                    '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                    // Replace `:0 0 0 0` with `:0`
                    '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                    // Replace `background-position:0` with `background-position:0 0`
                    '#(background-position):0(?=[;\}])#si',
                    // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                    '#(?<=[\s:,\-])0+\.(\d+)#s',
                    // Minify string value
                    '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                    '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                    // Minify HEX color code
                    '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                    // Replace `(border|outline):none` with `(border|outline):0`
                    '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                    // Remove empty selector(s)
                    '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
                ),
            array(
                    '$1',
                    '$1$2$3$4$5$6$7',
                    '$1',
                    ':0',
                    '$1:0 0',
                    '.$1',
                    '$1$3',
                    '$1$2$4$5',
                    '$1$2$3',
                    '$1:0',
                    '$1$2'
                ),
            $input
        );
    }
}

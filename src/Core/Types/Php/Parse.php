<?php

namespace RBFrameworks\Core\Types\Php;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Exceptions\CoreTypeException as Exception;

class Parse
{
    public static function parse($code)
    {
        try {
            if (substr($code, 0, 6) != '<?php ') {
                return self::parse_as_php($code, false);
            } else {
                return self::parse_as_php($code);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function parse_as_php($code, $closure = true, $tmpfile = null, $vars = array())
    {

        try {
            extract($vars);
            if ($closure) $code = '<?php ' . $code . ' ?>';
            $tmp = (!is_null($tmpfile) and is_dir($tmpfile)) ? fopen($tmpfile . md5(microtime()), 'w') : tmpfile();
            $tmpf = stream_get_meta_data($tmp);
            $tmpf = $tmpf['uri'];
            fwrite($tmp, $code);
            $ret = include($tmpf);
            fclose($tmp);
            return $ret;
        } catch (Exception $e) {
            echo "ERR";
            echo $e->getMessage();
        }
    }


    //Essa função insere o código em um arquivo, mas retorna o local do arquivo
    public static function parsed_file($code, $name = 'once', $ext = '.php')
    {

        $cache_dir = Config::get('location.cache.default');

        $arr = glob("{$cache_dir}{$name}_*{$ext}");
        foreach ($arr as $file) {
            if (time() - fileatime($file) > 3600) unlink($file);
        }

        $file = "{$cache_dir}{$name}_" . md5($code) . $ext;
        file_put_contents($file, $code);
        return $file;
    }
}

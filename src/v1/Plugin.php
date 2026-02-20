<?php

namespace Framework;

class Plugin {

    public static function load(string|array $functionname) {

        if(is_array($functionname)) {
            foreach ($functionname as $fn) {
                self::load($fn);
            }
            return;
        }

		if (!function_exists($functionname)) {

            $functionname = str_replace('\\', '/', $functionname);

			$include_class_paths = [
                get_root_path()."_app/functions/{$functionname}.php",
                get_root_path()."_app/functions/function.{$functionname}.php",
            ];

            if(strpos($functionname, '_') !== false) {
                $arr = explode('_', $functionname);
                $include_class_paths[] =  "_app/functions/{$arr[0]}.php";
            }

            foreach ($include_class_paths as $inc) {
                if (file_exists($inc)) {
                    include($inc);
                    return;
                }
            }

            throw new \Exception("$functionname not founded in [".implode('], [', $include_class_paths).']');
		}
    }

    public static function loadFromPath(string $path) {
        if(file_exists($path)) {
            include_once $path;
        }
        $path = str_replace(get_root_path(), '', $path);
        $path = ltrim($path, '/');
        $path = rtrim(get_root_path(), '/').'/'.$path;
        $path = str_replace('\\', '/', $path);
        include_once $path;
    }

}
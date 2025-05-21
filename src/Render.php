<?php

namespace Framework;

use Framework\Types\File;

abstract class Render {

    /**
     * Manages a global output buffer for a given asset name.
     *
     * If a buffer string is provided, it appends it to the existing buffer for the given name.
     * Optionally converts the buffer encoding based on the $forceUTF8 flag.
     * If the buffer is null, outputs and clears the buffer for the given name.
     *
     * @param string $name The name of the asset buffer to manage.
     * @param string|null $buffer The content to append to the buffer, or null to output and clear it.
     * @param bool|null $forceUTF8 Determines if the buffer should be encoded or decoded to UTF-8.
     *                              True to encode, false to decode, null for no conversion.
     */
    public static function content(string $name, string $buffer = null, bool $forceUTF8 = null) {
        global $assets_output_buffer;
        if(!isset($assets_output_buffer)) {
            $assets_output_buffer = [];
        }
        if(!isset($assets_output_buffer[$name])) {
            $assets_output_buffer[$name] = '';
        }
        if(!empty($buffer)) {
            
            if($forceUTF8 === true) $buffer = encoding($buffer);
            if($forceUTF8 === false) $buffer = encoding_reverse($buffer);
            $assets_output_buffer[$name].= "\r\n\t".$buffer;
        }
        if(is_null($buffer)) {
            echo $assets_output_buffer[$name];
            $assets_output_buffer[$name] = '';
            $assets_output_buffer[$name] = null;
            unset($assets_output_buffer[$name]);
        }
    }

    public static function contentFile(string $name, string $path, bool $forceUTF8 = null):void {
        $mime = File::getMimeType($path);
        $prefix = '';
        $suffix = '';
        if($mime == 'application/javascript' and strpos($path, 'module.js')) {
            if(file_exists($path)) {
                $prefix = "<script type='module'>";
            } else {
                $prefix = "<script type='module' src='{$path}'>";
            }
            $suffix = "</script>";
        } else
        if($mime == 'application/javascript' and strpos($path, 'module.js') === false) {
            $prefix = "<script>";
            $suffix = "</script>";
        } else
        if($mime == 'text/css') {
            $prefix = "<style>";
            $suffix = "</style>";
        }
        if(file_exists($path)) {
            self::content($name, $prefix.file_get_contents($path).$suffix, $forceUTF8);
        } else {
            self::content($name, $prefix.$suffix, $forceUTF8);
        }
    }

}
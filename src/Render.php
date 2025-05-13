<?php

namespace Framework;

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

}
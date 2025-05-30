<?php

namespace Framework;

use Framework\Types\Directory;
use Framework\Types\File;

abstract class Render {

    public static function dictReplaces(string $content, array $adicionalReplaces = []):string {
        $dict = Config::get('dict', []);
        $dict = array_merge($dict, $adicionalReplaces);
        $re = '/\{(?!\$)([\w\.]+)\}/';
        return preg_replace_callback($re, function($matches){
            return Config::get('dict.'.$matches[1], $matches[0]);
        }, $content);
    }

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
            $finalContent = $assets_output_buffer[$name];
            $finalContent = self::dictReplaces($finalContent);
            echo $finalContent;
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

    public static function streamFile(string $realfilepath, array $replaces = [], array $options = []) {
        if(!file_exists($realfilepath)) {
            throw new \Exception('File not found in Render::streamFile: '.$realfilepath);
        }

        $httpSite = Config::get('server.base_uri');

        //DefaultOptions
        $options = array_merge([
            'process' => true,
            'buffer' => null,
            'forceUTF8' => null,
            'assets_folder' => null,
            'filename' => null,
            'pathcallback' => function(string $fakepath) {
                return $fakepath;
            },
        ], $options);

        $getFakepath = function(bool $process = true) use ($realfilepath, $options, $replaces, $httpSite) {
            if(is_null($options['assets_folder'])) {
                $options['assets_folder'] = Config::get('location.cache.assets', get_root_path().'log/cache/assets/');
                $options['assets_folder'] = rtrim($options['assets_folder'], '\\');
                $options['assets_folder'] = rtrim($options['assets_folder'], '/');
                if(!is_dir($options['assets_folder'])) {
                    Directory::mkdir($options['assets_folder']);
                }
            }
            if(is_null($options['filename'])) {
                $options['filename'] = basename($realfilepath);
            }
            if(!$process) {
                return str_replace('//', '/', $httpSite.$options['pathcallback']($options['assets_folder'].'/'.$options['filename']));
            }
            if(!file_exists($options['assets_folder'].'/'.$options['filename']) or filemtime($realfilepath) > filemtime($options['assets_folder'].'/'.$options['filename'])) {
                $content = file_get_contents($realfilepath);
                if ($content === false) {
                    throw new \RuntimeException("Falha ao ler {$realfilepath}");
                }
                $content = self::dictReplaces($content, $replaces);
                if(file_put_contents($options['assets_folder'].'/'.$options['filename'], $content) === false) {
                    throw new \RuntimeException("Falha ao escrever");
                }
                //copy($realfilepath, $options['assets_folder'].'/'.$options['filename']);
            }
            return $options['pathcallback']($options['assets_folder'].'/'.$options['filename']);
        };

        if(!$options['process']) {
            return $getFakepath(false);
        }

        $mime = File::getMimeType($realfilepath);
        if($mime == 'application/javascript' and strpos($realfilepath, 'module.js')) {
            $prefix = "<script type='module' src='".$httpSite.$getFakepath()."'>";
            $suffix = "</script>";
        } else
        if($mime == 'application/javascript' and strpos($realfilepath, 'module.js') === false) {
            $prefix = "<script src='".$httpSite.$getFakepath()."'>";
            $suffix = "</script>";
        } else
        if($mime == 'text/css') {
            $prefix = "<link rel='stylesheet' href='".$httpSite.$getFakepath()."'>";
            $suffix = "";
        }

        if(is_null($options['buffer'])) {
            return $httpSite.$getFakepath();
        }

        self::content($options['buffer'], $prefix.$suffix, $options['forceUTF8']);
    }

}
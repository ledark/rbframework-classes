<?php 

namespace Framework;

class Config {

    public static function assigned(string $key, mixed $default = null):mixed {
        $info = self::getInfo($key);
        if(file_exists($info['path'])) {
            $inc = include($info['path']);
            if(is_null($info['key'])) {
                return $inc;
            }
            return Utils\Arrays::getValueByDotKey($info['key'], $inc, $default);
        }
        return $default;
    }

    public static function get(string $key, mixed $default = null):mixed {
        return self::assigned($key, $default);
    }

    public static function getInfo(string $key):array {
        $info = ['path' => null, 'key' => $key];

        if(is_file(rtrim(get_collection_path(), '/').'/'.$key.'.php')) {
            $info['path'] = rtrim(get_collection_path(), '/').'/'.$key.'.php';
            $info['key'] = null;
            return $info;
        }

        // Converte a string para uma estrutura de diretório
        $pathParts = explode('.', $key);

        // Verifica todas as combinações possíveis
        for ($i = count($pathParts); $i > 0; $i--) {
            // Cria um caminho baseado nos primeiros $i elementos do array
            $partialPath = implode(DIRECTORY_SEPARATOR, array_slice($pathParts, 0, $i));
            $filePath = $partialPath . '.php';

            // Verifica se o arquivo existe
            if (file_exists(get_collection_path().$filePath)) {
                $info['path'] = get_collection_path().$partialPath;
                $info['key'] = implode('.', array_slice($pathParts, $i));
            }
        }

        // Tenta o último caso: verifica se apenas a primeira parte com extensão .php existe
        if (file_exists(get_collection_path().$pathParts[0] . '.php')) {
            $info['path'] = get_collection_path().$pathParts[0] . '.php';
            $info['key'] = implode('.', array_slice($pathParts, 1));
        }

        // Se não encontrar nenhum arquivo correspondente, retorna false
        return $info;
    }
    
}
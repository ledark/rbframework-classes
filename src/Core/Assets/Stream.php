<?php

namespace RBFrameworks\Core\Assets;

use RBFrameworks\Core\Http;
use RBFrameworks\Core\Legacy\SmartReplace;

class Stream
{
    public static function files()
    {
        return ['version' => 0.0, 'lastupdate' => '2020-02-28'];
    }

    /**
     * Copia os arquivos de forma inteligente, protegendo o original e devolvendo o fakepath.
     * Você pode passar também um array de replaces para que quando o arquivo seja lido, um smart_replace seja aplicado.
     * A ideia do smart_replace aqui pode variar de acordo com a necessidade:
     * 
     *  filestream e httpfilestream são iguais, e aplicam o smart_replaces tradicional
     *  filestreamjs e httpfilestreamjs são iguais, e criam variáveis javascript de acordo.
     * 
     * @param string $realfilepath
     * @param array $replaces
     * @param method que define como o filestream deve agir
     *  '' O padrão é vazio como destrito em filestream/httpfilestream
     *  'javascript' faz o mesmo como descrito em filestreamjs/httpfilestreamjs
     *  'nostore' garante a exclusão prematura do arquivo, excluindo na próxima requisição de qualquer chamada a função filestream
     * @return string
     */
    public static function filestream(string $realfilepath, array $replaces = [], string $method = ''): string
    {
        self::filestreamclear();
        if (!file_exists($realfilepath)) trigger_error("file {$realfilepath} not exists");
        if (!is_dir('log/cache/')) trigger_error("directory log/cache/ not exists");

        $extension = '';
        if (strpos($realfilepath, '.') !== false) {
            $parts = explode('.', $realfilepath);
            $extension = '.' . array_pop($parts);
        }
        if ($method == 'nostore') $extension = '_nostore' . $extension;

        $fakepath = 'log/cache/fnfiles_' . md5($realfilepath) . $extension;

        if (count($replaces)) {

            $content = file_get_contents($realfilepath);

            if ($method == 'javascript') {
                $json = json_encode($replaces);
                $content = "var replaces = JSON.parse(`{$json}`);\r\n{$content}\r\n";
            } else {
                $content = SmartReplace::smart_replace($content, $replaces, true);
            }

            $fakepath = 'log/cache/fnfiles_' . md5($realfilepath) . '_repl' . md5($content) . $extension;
            file_put_contents($fakepath, $content);
            return $fakepath;
        }

        //Genera se Nao Existir
        if (!file_exists($fakepath)) {
            copy($realfilepath, $fakepath);
        }

        //Regera se Tamanho for Diferente
        if (filesize($fakepath) != filesize($realfilepath)) {
            copy($realfilepath, $fakepath);
        }

        return $fakepath;
    }

    public static function filestreamclear()
    {
        if (isset($GLOBALS['filestreamclear_executed'])) return false;
        $clearAfterSecs = 60 * 60 * 24;
        if (!is_dir('log/cache/')) trigger_error("directory log/cache/ not exists");
        $files = glob('log/cache/fnfiles_*');
        $timenow = time();
        foreach ($files as $file) {
            if (strpos($file, '_nostore') !== false) {
                unlink($file);
                continue;
            }
            if ($timenow - filectime($file) > $clearAfterSecs) {
                unlink($file);
                continue;
            }
        }
        $GLOBALS['filestreamclear_executed'] = true;
    }


    public static function httpfilestream(string $realfilepath, array $replaces = [])
    {
        return Http::getSite() . self::filestream($realfilepath, $replaces);
    }

    public static function filestreamjs(string $realfilepath, array $replaces = []): string
    {
        return self::filestream($realfilepath, $replaces, 'javascript');
    }

    public static function httpfilestreamjs(string $realfilepath, array $replaces = [])
    {
        return Http::getSite() . self::filestream($realfilepath, $replaces, 'javascript');
    }

    public static function tagfilestream(string $realfilepath, array $replaces = [], $type = 'auto', $param1 = ''): string
    {
        if ($type == 'auto') {
            $parts = explode('.', $realfilepath);
            $type = array_pop($parts);
        }
        switch ($type) {
            case 'html':
                return SmartReplace::smart_replace(file_get_contents($realfilepath), $replaces, true);
                break;
            case 'js':
                return '<script ' . $param1 . ' src="' . self::httpfilestream($realfilepath, $replaces) . '" type="text/javascript"></script>';
                break;
            case 'css':
                return '<link href="' . self::httpfilestream($realfilepath, $replaces) . '" rel="stylesheet" type="text/css">';
                break;
        }
    }

    public static function tagfilestreamjs_defer(string $realfilepath, array $replaces = [])
    {
        return self::tagfilestream($realfilepath, $replaces, 'js', 'defer');
    }
}

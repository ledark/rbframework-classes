<?php

namespace Core\Assets;

class Includes
{
    private $originalIncludeRequest;

    private static function extractExtensionWithFilters(string $arg):array {
        if( strpos($arg, '|') !== false ) {
            $params = explode('|', $arg);
            foreach($params as $i => $param) {
                if($i == 0) $file = $param;
                switch($param) {
                    case 'js':
                        $ext = "js";
                    break;
                    case 'nocachejs':
                        $ext = "js";
                    break;
                    case 'nocachecss':
                        $ext = "css";
                    break;
                    case 'jsinc':
                        $ext = "jsinc";
                    break;
                    case 'css':
                        $ext = "css";
                    break;
                    case 'ico':
                        $ext = "ico";
                    break;
                    default:
                        $ext = "php";
                    break;
                }
            }
            unset($param, $params, $i);
            
        //Chamada sem Filtros
        } else {
            $file = $arg;
            $arr = explode('.', $file);
            $ext = end( $arr );
            unset($arr);
        }
        return [
            'file' => $file,
            'ext' => $ext,
        ];
    }

    private static function hasIncluded(string $arg):bool {
        global $RBVars;
        if(!isset($RBVars) and !is_array($RBVars)) $RBVars = [];
        if(!isset($RBVars['Requireds'])) $RBVars['Requireds'] = array();    
        if(in_array($arg, $RBVars['Requireds'])) return true; 
        return false;       
    }

    private static function registerAsIncluded(string $arg):void {
        global $RBVars;
        $RBVars['Requireds'][] = $arg;       
    }

    private static function extractForceCache(string $arg):array {
        $forceCache = true;
        
        if(substr($arg, -9) == "nocachejs" ) {
            $forceCache = false;
            $arg = substr($arg, 0, strlen($arg)-10);
        }
        
        if(substr($arg, -10) == "nocachecss" ) {
            $forceCache = false;
            $arg = substr($arg, 0, strlen($arg)-11);
        }
        
        return [
            'file' => $arg,
            'forceCache' => $forceCache,
        ];
    }

    private static function isRemote(string $arg):bool {       
        $remote = false;
        if( substr($arg, 0, 7) == "http://" ) {
            $remote = true;
        } else 
        if( substr($arg, 0, 8) == "https://" ) {
            $remote = true;
        } else 
        if( substr($arg, 0, 2) == "//" ) {
            $remote = true;
        }
        return $remote;
    }

    private static function includeLocal(string $arg) {
        $fileInfo = self::extractExtensionWithFilters($arg);
        switch($fileInfo['ext']) {
            case 'css':
                self::include_css($fileInfo['file']);
            break;
            case 'js':
                self::include_js($fileInfo['file']);
            break;
            case 'jsinc':
                self::include_js($fileInfo['file'], true);
            break;
            case 'ico':
                self::include_ico($fileInfo['file']);
            break;
            default:
                if(!file_exists($fileInfo['file'])) throw new \Exception($fileInfo['file']." failt to be includedLocale");
                include($fileInfo['file']);
            break;
        }        
    }

    private static function includeRemote(string $arg) {
        $fileInfo = self::extractExtensionWithFilters($arg);
        switch ($fileInfo['ext']) {
            case 'css':
                echo("\t".'<link href="'.$fileInfo['file'].'" rel="stylesheet" type="text/css">'."\n");
            break;
            case 'js':
                echo("\t".'<script src="'.$fileInfo['file'].'" type="text/javascript"></script>'."\n");	
            break;
            case 'jsinc':
                echo("\t".'<script src="'.$fileInfo['file'].'" type="text/javascript"></script>'."\n");	
            break;
            default:
                throw new \Exception("includeRemote not implemented");
            break;
        }        
    }

    /*
    function include_component($file) {

        $f = "{$file}.config.php"; 		if($file_exists($f)) include($f);
        $f = "{$file}.angular.js"; 		if($file_exists($f)) include_js($f);
        $f = "{$file}.php"; 			if($file_exists($f)) include($f);
        $f = "{$file}.html"; 			if($file_exists($f)) include($f);
        $f = "{$file}.js"; 				if($file_exists($f)) include_js($f);
        $f = "{$file}.css"; 			if($file_exists($f)) include_css($f);
    }
    */

    /*
    A função recebe o arquivo, que podem ser incluido com base em filters
    Para incluir com um filter use, por exemplo include_conditional("arquivo.ts|js") ou include_conditional("arquivo-de-stylesheet|css")
    Se o nome do arquivo possuir http(s)://, ele faz o include remoto. Caso contrário, inclui o arquivo normalmente.
    O foco dessa função é fazer o include js e css.
    Se houver necessidade de múltiplas execuções, então é necessário limpar a variável global $RBVars['Requireds'] após a chamada.
    */    
    public static function include_conditional(string $arg, bool $forceCache = null):bool {
        
        if (is_null($forceCache)) {
            $forceCache = self::extractForceCache($arg)['forceCache'];
            $arg = self::extractForceCache($arg)['file'];
        }

        if(self::hasIncluded($arg)) return false;

        if(self::isRemote($arg)) {
            self::includeRemote($arg);
        } else {
            self::includeLocal($arg);
        }

        self::registerAsIncluded($arg);
        return true;
    }
   
    /*
    function smart_include($file) {
        global $httpSite, $RBFolders;
        $file2include = array(
            "{$file}.class.php"
        ,	"{$file}.config.php"
        ,	"{$file}.style.php"
        ,	"{$file}.css"
        ,	"{$file}.script.js"
        ,	"{$file}.js"
        ,	"{$file}.php"
        );
        foreach($file2include as $inc) {
            include_auto($inc);
        }
    }
    
    function include_auto($file) {
        if(!file_exists($file)) {
            global $RBFolders;
            
            if(!isset($RBFolders['dirRB'])) $RBFolders['dirRB'] = 'htm/';
            
            $file = $RBFolders['dirRB'].$file;
        }
        if(!file_exists($file)) {
            return false;
        }	
        
        $ext = explode('.', $file, 2);
        switch( end($ext) ) {
            case 'js': 
                include_js($file);
            break;
            case 'css': 
                include_css($file);
            break;
            case 'script.php': 
                echo "<script language=\"javascript\" type=\"text/javascript\">\n"; include($file); echo "\n</script>";
            break;
            case 'style.php': 
                echo "<style type=\"text/css\">\n"; include($file); echo "\n</style>";
            break;
            default:
                include($file);
            break;
        }
    }
    
    */

    private static function include_html(string $file) {
        if(!file_exists($file)) throw new \Exception("Failed to Include [$file] HTML");
        include($file);
    }
    private static function include_css(string $file) {
        if(!file_exists($file)) throw new \Exception("Failed to Include [$file] CSS");
        echo '<style>';
        include($file);
        echo '</style>';
    }
    private static function include_js(string $file, bool $infline = false) {
        if(!file_exists($file)) throw new \Exception("Failed to Include [$file] JS");
        echo '<script>';
        include($file);
        echo '</script>';
    }
    private static function include_ico(string $file) {
        if(!file_exists($file)) throw new \Exception("Failed to Include [$file] ICO");
        include($file);
    }    

    /*
    
    //Fun��o include_html Adicionada em 03/11/2016
    function include_html($file) {
        global $httpSite, $RBFolders;
        $file2include = $RBFolders['dirRB'].$file;
        if(file_exists($file2include.'.html')) {
            include($file2include.'.html');
            include_js($file2include.'.js');
            include_css($file2include.'.css');
        }
    }
    function include_css($file) {
        global $httpSite;
        if(isset($GLOBALS['scriptCaches']) and $GLOBALS['scriptCaches']) $cache = '?'.time(); else $cache = '';
        if(file_exists($file))
        echo("\t".'<link href="'.$httpSite.$file.$cache.'" rel="stylesheet" type="text/css">'."\n");
    }
    
    function include_ico($file) {
        global $httpSite;
        if($GLOBALS['scriptCaches']) $cache = '?'.time(); else $cache = '';
        if(file_exists($file))
        echo("\t".'<link rel="icon" type="image/x-icon" href="'.$httpSite.$file.$cache.'" />'."\n");			
    }
    
    function include_js($file, $inline = false) {
    
        global $httpSite;
        if(!isset($httpSite)) {
            global $RBVars;
            $httpSite = $RBVars['httpSite'];
        }
        if(isset($GLOBALS['scriptCaches']) and $GLOBALS['scriptCaches']) $cache = '?'.time(); else $cache = '';
        if(file_exists($file)) {
            if($inline) {
                echo "\t".'<script type="text/javascript">'."\r\n";
                include($file);
                echo "\r\n".'</script>'."\r\n";					
            } else {
                echo("\t".'<script src="'.$httpSite.$file.$cache.'" type="text/javascript"></script>'."\n");
            }
        }
    
    
    
    }
    function includeAjax($file, $post = '', $id = null, $callback = '') {
        global $httpLink, $httpSite;
        $file = str_replace('htm/', '', $file);
        $file = rtrim($file, '.php');
        if(is_null($id)) $id = md5($file);
        $nobars = @end(explode('/', $file));
        if(is_array($post)) {
            $postin = ', { ';
            foreach($post as $chave => $valor) {
                $valor = (is_string($valor)) ? "'{$valor}'" : "$valor";
                $postin.= "{$chave}:{$valor}, ";
            }
            $postin = rtrim($postin, ', ');
            $postin.= '} ';
        }
        if(empty($postin)) {
            $postin = '';
        }
        
        if(!empty($callback)) {
            $callback = ', function() { '.$callback.' }';
        }
        
        echo '<div id="'.$id.'" class="inc-'.$nobars.'"><img src="'.$httpSite.'img/sys/200.gif"/></div>';
        echo "<script language=\"javascript\" type=\"text/javascript\"> $('#{$id}').load('{$httpSite}{$file}&noFilter=1'{$postin}{$callback}); </script>";		
    }
    
    //include_dir Adicionado em 20/10/2017
    /*
        Faz um include dos arquivos presentes em um diret�rio, usando como filtro o final do nome do arquivo que deve coincidir com $filter
        Se precisar encapsular os arquivos dentro de scripts ou styles, prefira a vers�o antiga includedir() ao inv�s dessa include_dir()
        Exemplo: include_dir("htm/path/to/folder", ".head.php"); //Incluir� apenas o arquivos com a extens�o .head.php
        Exemplo: include_dir("htm/path/to/folder/", ".js", "<script>", "</script>"); //Incluir� apenas o arquivos com a extens�o .js j� encapsulados em um script
        Exemplo: include_dir("htm/path/to/folder", ".css", "<style>", "</>");
        
    */
    /*
    function include_dir($dir, $filter = "", $prefix = "", $suffix = "") {
        if(!is_dir($dir)) {
            global $RBFolders;
            $dir = $RBFolders['dirRB'].'/'.$dir;
        }
        if(!is_dir($dir)) {
            echo "$dir n�o � diret�rio.";
            return false;
        }
        echo $prefix;
        foreach (new DirectoryIterator($dir) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $name = $fileInfo->getFilename();
            if(empty($filter)) {
                include($fileInfo->getPathname());
            } else
            if( substr($name, strlen($filter)*-1 ) == $filter ) {
                include($fileInfo->getPathname());
            }
        }
        echo $suffix;
    }
    
    function includedir($dir) {
        global $httpSite;
        if($GLOBALS['scriptCaches']) $cache = '?'.time(); else $cache = '';
        if(is_dir($dir)) {
            $d = dir($dir);
            while (false !== ($entry = $d->read())) {
                if ($entry!="." and $entry!=".." and $entry!="start.php") {
                    if(!is_dir("$diretorio/$entry")) {
                        //Descobre Extens�o
                        $ext = end(explode('.',$entry));
                        ob_start();
                        switch($ext) {
                            case 'js':
                                echo '<script src="'.$httpSite.$dir.'/'.$entry.$cache.'" type="text/javascript"></script>'."\n";
                            break;
                            case 'css':
                                echo '<link href="'.$httpSite.$dir.'/'.$entry.$cache.'" rel="stylesheet" type="text/css">'."\n";
                            break;	
                            case 'php':
                                include($dir.'/'.$entry);
                            break;
                        }
                    }
                }
            }
        }
    }
    */
    /* Faz um include acrescentando basicas substitui��es para src e href relativos */
    /*
    function include_base($file, $base = null, $mode = 'include') {
        
        if(defined('CACHE') and CACHE == true) {
            $filec = "log/cache/".md5($file).'.php';
            if(file_exists($filec)) {
                include($filec);
                return true;
            }   
        }
        
        if(is_null($base)) $base = HTTPSITE;
        
        //HTML Code
        if($mode == 'include') {
            ob_start();
            include($file);
            $html = ob_get_clean();
        } else {
            $html = file_get_contents($file);
        }
        
        //Relative to Absolute
        $re = '/[^ng-](href|src)\s?=\s?["|\']((?!mailto|http|\/\/).*?)["|\']/mU';
        $subst = ' $1="'.$base.'$2"';
        $html = preg_replace($re, $subst, $html);
        
        if(defined('CACHE') and CACHE == true) {
            file_put_contents($filec, $html);
        }
        
        echo $html; 
        exit();   
    }
*/    
}

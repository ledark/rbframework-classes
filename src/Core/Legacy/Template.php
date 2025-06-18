<?php 

/*
	Utiliza os arquivos da pasta _app/tmpl/ para gerar conteúdo

	Exemplo:
	Template::usar('Controller.php');
	$tmpl = new Template();
 * 
 * 
	
	
*/

namespace RBFrameworks\Core\Legacy;

use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Utils\Replace;
use RBFrameworks\Core\Types\Php\Parse;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Cache;
use Exception;

class Template {
	
	public static $tmpl = "";
    
    /*
     * Utilizado internamente como include_path
     */
    private static function search_tmpl($str) {
        return Cache::stored(function() use ($str) {
            $searchDefaultFolders = [
                __DIR__ . "/Templates/",
                __DIR__ . "/Templates/Legacy/",
                "_app/class/user/class.Template/",
                "_app/class/user/class.Form/",
                __DIR__."/class.Template/Pieces/",
                __DIR__ . "/Templates/Pieces/",
                __DIR__ . "/Templates/Legacy/Pieces/",
                "_app/class/user/class.Template/Pieces/",
                "_app/class/user/class.Form/Pieces/",
                __DIR__."/class.Template/Pieces/",
            ];
            $searchBacktraceFolders = [];
            foreach(Debug::getFileBacktrace() as $level => $file_line) {
                $file_line = explode(':', $file_line);
                $searchBacktraceFolders[] = dirname($file_line[0]).'/';
                $searchBacktraceFolders[] = dirname($file_line[0]).'/Pieces/';
            }
            $tmpl = new File($str);
            $tmpl->addSearchExtension('.tmpl');
            $tmpl->addSearchFolders(array_merge($searchDefaultFolders, $searchBacktraceFolders));
            return $tmpl->hasFile() ? $tmpl->getFilePath() : false;
        }, 'tmpla_'.md5($str), 60*60*24);
    }
	
    //@deprecate
	public static function tmpl($value) {
		self::$tmpl = $value;
        return new self;
	}
	
    //@deprecate
	public static function render() {
		echo self::$tmpl;
	}
	
    /*
     * Retonar o conteúdo de um template, aplicando self::replace.
     * Melhor utilizado para templates estáticos, ou cujas mudanças sejam somentes textuais, ou seja, de template
     */
	public static function usar($tmpl, $replaces = array(), $chunk = null):string {
        $tmpl = self::search_tmpl($tmpl);
        if(!file_exists($tmpl)) exit("O arquivo necessário $tmpl não foi encontrado.");
        $content = file_get_contents($tmpl);
        if(is_string($chunk) and !empty($chunk)) {
            if(strpos($chunk, ':') !== false) {
                $content = self::chunkcomment($content, $chunk, $replaces);
            } else {
                $content = self::chunk($content, $chunk, $replaces);
            }
        }
		ob_start();
		self::parse($content, $replaces);
		return self::replace(null, $replaces, true);
	}
	
    /*
     * Dado um $file, ele irá copiar um template existente nele.
     * Funciona de modo parecido com usar(), exeto que não retornará nada na tela, apenas salvará em um arquivo
     */
	public static function clonar($file, $tmpl, $replaces = array(), $override = true) {
        
       
        $tmpl = self::search_tmpl($tmpl);
        if($tmpl !== false) {            
            $content = self::replace(file_get_contents($tmpl), $replaces, true);
            if($override) {
                file_put_contents($file, $content);
            } else { 
                if(!file_exists($file)) file_put_contents($file, $content);
            }
        }

	}
    
    /*
     * Para utilizar apenas uma tag específica de uma $string, a função chunk pode ser utilizada.
     * Se a tag a ser puxada (chunkada), for prefixada em :php, então um eval será utilizado ao invés do padrão self::replace.
     */
    public static function chunk($string, $tag, array $replaces = []) {
        preg_match("/<$tag>(.*?)<\/$tag>/is", $string, $Matches);
        
        //Requisições php <tagname:php></tagname:php>
        if(substr($tag, strlen($tag)-4) == ':php' ) {
            ob_start();
            eval($Matches[1]);
            return ob_get_clean();
        
        //Requisições simples <tagname></tagname>
        } else {
            return $Matches[1];    
        }
        
        
    }
    
    public static function chunkcomment($string, $tag, array $replaces = []) {
        preg_match("/<!--$tag-->(.*?)<!--$tag-->/is", $string, $Matches);
        
        //Requisições php <tagname:php></tagname:php>
        if(substr($tag, strlen($tag)-4) == ':php' ) {
            ob_start();
            eval($Matches[1]);
            return ob_get_clean();
        
        //Requisições simples <tagname></tagname>
        } else {
            return $Matches[1];    
        }
        
        
    }

    public static function replace(string $content = null, array $replaces = [], bool $literal = false):string {
        if(is_null($content)) $content = ob_get_clean();
        $replace = new Replace($content, $replaces);
        if($literal) {
            $replace->useLiteral();
        } else {
            $replace->ignoreLiteral();
        }
        return $replace->render(true);
    }

    public static function parse($code, $vars = array()) {
        try {
            if(substr($code, 0, 6) != '<?php ') {
                return self::parse_as_php($code, false, null, $vars);
            } else {
                return self::parse_as_php($code, true, null,  $vars);
            }
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }
    
    public static function parse_as_php($code, $closure = true, $tmpfile = null, $vars = array()) {    
        
        try {
            extract($vars);
            if($closure) $code = '<?php '.$code.' ?>';
            $tmp = (!is_null($tmpfile) and is_dir($tmpfile)) ? fopen($tmpfile.md5(microtime ()), 'w') : tmpfile();
            $tmpf = stream_get_meta_data ( $tmp );
            $tmpf = $tmpf ['uri'];
            fwrite ( $tmp, $code );
            $ret = include ($tmpf);
            fclose ( $tmp );
            return $ret;
        } catch(Exception $e) {
            echo "ERR";
            echo $e->getMessage();
        }
    }
    
    
    //Essa função insere o código em um arquivo, mas retorna o local do arquivo
    public static function parsed_file($code, $name = 'once', $ext = '.php') {
        
        $arr = glob("log/cache/{$name}_*{$ext}");
        foreach($arr as $file) {
            if(time() - fileatime($file) > 3600) unlink($file);
        }
        
        $file = "log/cache/{$name}_".md5($code).$ext;
        file_put_contents($file, $code);
        return $file;
    }    
	
}
<?php

namespace RBFrameworks\Helpers;

/*
Desenvolvido por: Ricardo[at]Bermejo.com.br
Última Atualização: 06/02/2019
*/
class Strings {
    
    public static function deformar_email(string $email): string {
        $em   = explode("@",$email);
        $name = implode(array_slice($em, 0, count($em)-1), '@');
        $len  = floor(strlen($name)/2);
        return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);          
    }
    
    public function obfuscate_email($email, $encode = 1, $reverse = 0, $before = '<span class="email">', $after = '</span>') {
 $output = '';
 if ($reverse) {
  $email = strrev($email);
  $output = $before;
 }
 if ($encode) {
  for ($i = 0; $i < (strlen($email)); $i++) {
   $output .= '&#' . ord($email[$i]) . ';';
  }
 } else {
  $output .= $email;
 }
 if ($reverse) {
  $output .= $after;
 }
 return $output;
}    
    
    public static function isCPF(string $string): bool {
        $string = trim($string);
        $string = self::clear($string);
        return (strlen($string) == 11) ? true : false;
    }
    public static function isCNPJ(string $string): bool {
        $string = trim($string);
        $string = self::clearCNPJ($string);
        return (strlen($string) == 14 or strlen($string) == 15) ? true : false;
    }
    
    public static function pathSanatize(string $path, $rtrim = false): string {
        $path = str_replace('\\', '/', $path);
        $path = str_replace('//', '/', $path);
        $path = (substr(trim($path), -1) == '/') ? $path : $path.'/';
        if($rtrim) $path = rtrim($path, '/');
        if(!is_dir($path)) \Logger::info("O caminho $path chamado por Strings::pathSanatize não é um diretório válido");
        return $path;
    }
    
    public static function encapsule(string $string, string $prefix = '', string $sufix = ''): string {
        return $prefix.$string.$sufix;
    }
    
    /**
     * Essa função tenta injetar alguma string $inject dentro da string $text, usando o ponto da injeção as strings de $startText e $endText
     * @param string $inject Conteúdo a ser Injetado
     * @param string $text TextoBase onde será injeato
     * @param string $startText que possui o token que caracteriza a abertura da piece
     * @param string $endText que possui o token que caracteriza o fechamento da piece
     * @return string
     */
    public static function injectPiece(string $inject, string $text, string $startText, string $endText, bool $preserveCapsule = false):string {
        $posStartText = strpos($text, $startText);
        $posEndText = strpos($text, $endText);
        if($posStartText !== false and $posEndText !== false and $posStartText < $posEndText) {
            $texthead = substr($text, 0, $posStartText);
            $textfoot = substr($text, $posEndText+strlen($endText));
            if($preserveCapsule) $inject = $startText.$inject.$endText;
            return $texthead.$inject.$textfoot;
        } else {
            return $text;
        }
    }

    /**
     * Essa função tenta pegar um pedaço de uma string $text que esteja entre dois textos.
     * Por exemplo, para trazer o que estiver em uma tag head: Strings::getPiece('<head>', '</head>', $htmlCode);
     * @param string $startText que possui o token que caracteriza a abertura da piece
     * @param string $endText que possui o token que caracteriza o fechamento da piece
     * @param string $text que possui a string a ser verificada
     * @return string contendo o pedaço (piece) do código que estava entre o startText e o endText
     */
    public static function getPiece(string $startText, string $endText, string $text): string {
        $posStartText = strpos($text, $startText);
        $posEndText = strpos($text, $endText);
        if($posStartText !== false and $posEndText !== false and $posStartText < $posEndText) {
            return substr($text, $posStartText, $posEndText-$posStartText);
        } else {
            return $text;
        }
    }

    public static function stringfyVariable(): string {
        $args = func_get_args();
        $return = "";
        if(count($args)) {
            foreach($args as $mixed) {
                ob_start();
                print_r($mixed);
                $return.= ob_get_clean();
            }
        }
        return $return;

    }

    public static function clearAcentos($string) {
        if ( !preg_match('/[\x80-\xff]/', $string) )
             return $string;

         $chars = array(
         // Decompositions for Latin-1 Supplement
         chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
         chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
         chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
         chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
         chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
         chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
         chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
         chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
         chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
         chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
         chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
         chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
         chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
         chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
         chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
         chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
         chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
         chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
         chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
         chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
         chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
         chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
         chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
         chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
         chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
         chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
         chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
         chr(195).chr(191) => 'y',
         // Decompositions for Latin Extended-A
         chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
         chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
         chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
         chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
         chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
         chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
         chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
         chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
         chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
         chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
         chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
         chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
         chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
         chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
         chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
         chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
         chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
         chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
         chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
         chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
         chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
         chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
         chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
         chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
         chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
         chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
         chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
         chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
         chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
         chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
         chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
         chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
         chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
         chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
         chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
         chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
         chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
         chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
         chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
         chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
         chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
         chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
         chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
         chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
         chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
         chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
         chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
         chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
         chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
         chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
         chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
         chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
         chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
         chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
         chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
         chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
         chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
         chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
         chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
         chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
         chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
         chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
         chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
         chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
         );

         $string = strtr($string, $chars);

         return $string;
    }
    
    public static function cents2float($intval) {
        $float = $intval/100;
        return number_format($float, 2, '.', '');
    }
    public static function cents2moeda($intval) {
        $float = $intval/100;
        return number_format($float, 2, ',', '.');
    }
    
    public static function clearMoeda($string) {
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = str_replace('_', '', $string);
        $string = str_replace('.', '', $string);
        $string = str_replace(',', '', $string);
        $string = str_replace('R', '', $string);
        $string = str_replace('$', '', $string);
        $string = str_replace('#', '', $string);
        return $string;
    }
    
    public static function clearCNPJ($string) {
        $string = str_replace('.', '', $string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = str_replace('_', '', $string);
        $string = str_replace('/', '', $string);
        return $string;
    }    
    
    /*
     * Formatação de Máscaras
     * Varre cada caracter de $mask aplicando-a em val
     * Exemplos:
     * 
        $cnpj = "11222333000199";
        $cpf = "00100200300";
        $cep = "08665110";
        $data = "10102010";
     * 
        echo Strings::mask($cnpj,'##.###.###/####-##');
        echo Strings::mask($cpf,'###.###.###-##');
        echo Strings::mask($cep,'#####-###');
        echo Strings::mask($data,'##/##/####');
     * 
     */
    public static function mask($string, $mask) {
        switch($mask) {
            case 'cpf':
                $mask = "%s%s%s.%s%s%s.%s%s%s-%s%s";
            break;
            case 'cnpj':
                $mask = "%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s";
            break;
            case 'emailhash':
                $email = explode('@', $string);
                $conta = $email[0];
                $contahash = "";
                for($i=0; $i<=strlen($conta); $i++) {
                    if($i<3) $contahashed = '*'; else $contahashed = $conta[$i];
                    
                    $contahash.= $contahashed;
                }
                $dominio = $email[1];
                return $contahash.'@'.$dominio;
            break;;
        }
        return vsprintf($mask, str_split($string));
    }
    
    public static function clear($input, $mask = ".-_#") {
        for($i=0; $i<= strlen($mask); $i++) {
            if(isset($mask[$i])) {
                $input = str_replace($mask[$i], "", $input);
            }
        }
        return $input;
    }
    
	
	public function tempo($int, $tags = "atras") {
		plugin("tempo");
		$string = "";
		$tags = explode('|', $tags);
		foreach($tags as $tag){
			if($tag == 'atras') 		$string = formatar_tempo($int);
		}
		return $string;
	}
	
	
	/*
		Strings::human_filesize($bytes);
	*/
	public static function human_filesize($bytes, $decimals = 2) {
		$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}
	    
	
	/*
		Strings::geolocate_uf2estado('SP');
		Strings::geolocate_estado2uf('São Paulo');
	*/
	private function geolocate_getestados($invert = false) {
		$estados = array(
			'AC'	=>	'Acre'
		,	'AL'	=>	'Alagoas'
		,	'AM'	=>	'Amazonas'
		,	'AP'	=>	'Amapá'
		,	'BA'	=>	'Bahia'
		,	'CE'	=>	'Ceará'
		,	'DF'	=>	'Distrito Federal'
		,	'ES'	=>	'Espírito Santo'
		,	'GO'	=>	'Goiás'
		,	'MA'	=>	'Maranhão'
		,	'MT'	=>	'Mato Grosso'
		,	'MS'	=>	'Mato Grosso do Sul'
		,	'MG'	=>	'Minas Gerais'
		,	'PA'	=>	'Pará'
		,	'PB'	=>	'Paraíba'
		,	'PR'	=>	'Paraná'
		,	'PE'	=>	'Pernambuco'
		,	'PI'	=>	'Piauí'
		,	'RJ'	=>	'Rio de Janeiro'
		,	'RN'	=>	'Rio Grande do Norte'
		,	'RO'	=>	'Rondônia'
		,	'RS'	=>	'Rio Grande do Sul'
		,	'RR'	=>	'Roraima'
		,	'SC'	=>	'Santa Catarina'
		,	'SE'	=>	'Sergipe'
		,	'SP'	=>	'São Paulo'
		,	'TO'	=>	'Tocantins'
		);
		if($invert) {
			return array_flip($estados);
		} else {
			return $estados;
		}
	}
	
	public function geolocate_uf2estado($uf = null) {
		$uf = (is_null($uf)) ? $_REQUEST['str'] : $uf;
		$array = self::geolocate_getestados();
		return $array[strtoupper($uf)];
	}
	
	public function geolocate_estado2uf($estado = null) {
		$estado = (is_null($estado)) ? $_REQUEST['str'] : $estado;
		$array = self::geolocate_getestados(true);
		foreach($array as $i => $r) {
			$arrayest[strtoupper($i)] = $r;
		}
		return $arrayest[strtoupper($estado)];
	}
	
	/*
		Strings::string_format($string, 'strip_tags|nl2br|stripslashes');
		Strings::string_format($string, 'br2nl|addslashes');
		Strings::int_format($timeunix, date); //Retornará 14/03/1986
		Strings::int_format($timeunix, fulldate); //Retornará 14/03/1986 às 12:15:20
	*/
	public function string_format($string, $tags = 'strip_tags|nl2br|stripslashes') {
		$tags = explode('|', $tags);
		foreach($tags as $tag){
			if($tag == 'strip_tags') 		$string = strip_tags($string);
			if($tag == 'nl2br') 			$string = nl2br($string);
			if($tag == 'br2nl') 			$string = self::br2nl($string);
			if($tag == 'stripslashes') 		$string = stripslashes($string);
			if($tag == 'addslashes') 		$string = addslashes($string);
			if($tag == 'capitalize') 		$string = ucwords( strtolower($string) );
			if(substr($tag, 0, 6) == 'limit:' ) $string = substr($string, 0, substr($tag, 6));
		}
		return $string;
	}
	
	function br2nl($string) {
		$string = str_replace('<br/>', "\r\n", $string);
		$string = str_replace('<br>', "\r\n", $string);
		return $string;
	}
	
    public static function unescape($string):string {
        return stripcslashes($string);
        $string = str_replace('\t', "\t", $string);
        $string = str_replace('\r', "\r", $string);
        $string = str_replace('\n', "\n", $string);
        return $string;
    }
    
	static function int_format($int, $tags = 'date') {
		$tags = explode('|', $tags);
		foreach($tags as $tag){
			if($tag == 'date') return date('d/m/Y', $int);
			if($tag == 'fulldate') return date('d/m/Y à\s H:i:s', $int);
			if($tag == 'intval') {
				$string = $int;
				$string = trim($string);
				$string = str_replace('R', '', $string);
				$string = str_replace('$', '', $string);
				$string = str_replace(' ', '', $string);
				$string = str_replace('-', '', $string);		
				$string = str_replace('.', '', $string);		
				$string = str_replace(',', '', $string);
				return $string;
			}
            if($tag == 'moeda') {
                return number_format(self::int_format($string, 'intval'), 2, ',', '.');
            }
            if($tag == 'moedafloat') {
                return number_format(self::int_format($string, 'intval'), 2, '.', '');
            }
		}
	}
	
	static function var_format(&$var, $tags) {
		$tags = explode('|', $tags);
		foreach($tags as $tag){
			if($tag == 'date')	{
				$var = date('d/m/Y', $var);
			} else 
			if($tag == 'fulldate') {
				$var = date('d/m/Y à\s H:i:s', $var);
			} else {
				$var = self::string_format($var, $tag);
			}
		}
	}
	
    static function mysql_escape_mimic($inp) {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }     
		
	/*
		//Tenta arrumar um HTML, tirando a tag de doctype e outras tags abertas
		function html_parse($html, $return = 'STRING') {
			preg_match_all("/(<([\w]+)[^>]*>)(.*?)(<\/\\2>)/", $html, $out);
			switch($return) {
				case 'STRING':
					foreach($out[0] as $tags ) $html_parsed.= $tags;
					return $html_parsed;
				break;
				case 'ARRAY':
					return $out[0];
				break;
			}
		}
	
		//Converte HTML em Array
		function html2a ( $html ) {
		  if ( !preg_match_all( '
		@
		\<\s*?(\w+)((?:\b(?:\'[^\']*\'|"[^"]*"|[^\>])*)?)\>
		((?:(?>[^\<]*)|(?R))*)
		\<\/\s*?\\1(?:\b[^\>]*)?\>
		|\<\s*(\w+)(\b(?:\'[^\']*\'|"[^"]*"|[^\>])*)?\/?\>
		@uxis', $html = trim($html), $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER) )
			return Strings::html_parse($html, 'ARRAY');
		  $i = 0;
		  $ret = array();
		  foreach ($m as $set) {
			if ( strlen( $val = trim( substr($html, $i, $set[0][1] - $i) ) ) )
			  $ret[] = $val;
			$val = $set[1][1] < 0
			  ? array( 'tag' => strtolower($set[4][0]) )
			  : array( 'tag' => strtolower($set[1][0]), 'val' => html2a($set[3][0]) );
			if ( preg_match_all( '
		/(\w+)\s*(?:=\s*(?:"([^"]*)"|\'([^\']*)\'|(\w+)))?/usix
		', isset($set[5]) && $set[2][1] < 0
		  ? $set[5][0]
		  : $set[2][0]
		  ,$attrs, PREG_SET_ORDER ) ) {
			  foreach ($attrs as $a) {
				$val['attr'][$a[1]]=$a[count($a)-1];
			  }
			}
			$ret[] = $val;
			$i = $set[0][1]+strlen( $set[0][0] );
		  }
		  $l = strlen($html);
		  if ( $i < $l )
			if ( strlen( $val = trim( substr( $html, $i, $l - $i ) ) ) )
			  $ret[] = $val;
		  return $ret;
		}

		//Converte Array em HTML
		function a2html ( $a, $in = "" ) {
		  if ( is_array($a) ) {
			$s = "";
			foreach ($a as $t)
			  if ( is_array($t) ) {
				$attrs="";
				if ( isset($t['attr']) )
				  foreach( $t['attr'] as $k => $v )
					$attrs.=" ${k}=".( strpos( $v, '"' )!==false ? "'$v'" : "\"$v\"" );
				$s.= $in."<".$t['tag'].$attrs.( isset( $t['val'] ) ? ">\n".a2html( $t['val'], $in."  " ).$in."</".$t['tag'] : "/" ).">\n";
			  } else
				$s.= $in.$t."\n";
		  } else {
			$s = empty($a) ? "" : $in.$a."\n";
		  }
		  return $s;
		}		
	
/*		
		function find_tag($tag = "\w", $string) {
			
			 
		preg_match_all("/(<([{$tag}]+)[^>]*>)(.*?)(<\/\\2>)/", $string, $matches, PREG_SET_ORDER);
			
			print_r($matches);
			
		}
*/		
}
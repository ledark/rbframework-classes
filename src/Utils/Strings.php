<?php 

namespace Framework\Utils;

class Strings {

    public static function viewlimit($string, $limit = 100, $vermais = '...') {
        return (strlen($string) > $limit) ? substr($string, 0, $limit-strlen($vermais)).$vermais : $string;
    }

    public static function vermais($string, $limit = 100, $vermais = '...') {
        return (strlen($string) > $limit) ? substr($string, 0, $limit-strlen($vermais)).$vermais : $string;
    }
    
    public static function deformar_email(string $email): string {

        $emailStr = "";

        $emailParts = explode('@', $email);
        $emailName = $emailParts[0];
        $fullDomainName = $emailParts[1];
        $domainParts = explode('.', $fullDomainName);

        
        $obfuscate = function($str):string {
            $retStr = "";
            foreach(str_split($str) as $i => $ch) {
                $parts = floor(strlen($str)/3);
                if($i >= $parts and $i <= $parts*2) $ch = "*";
                $retStr.= $ch;
            }
            return $retStr;
        };

        $emailStr.= $obfuscate($emailName).'@'.$obfuscate($fullDomainName);


        return $emailStr;

        $posSeparator = strpos($email, '@');
        $maxLen = strlen($email);

        $pos = 0;
        





        $em   = explode("@", $email);
        $arr = array_slice($em, 0, count($em)-1);
        $name = implode('@', $arr);
    
        if(strlen($name)==1){
            return   '*'.'@'.end($em);
        }
    
        $len  = floor(strlen($name)/2);
    
        return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);
    }

    public static function maskFone(string $telefone):string {
        $digits = preg_replace("/[^0-9]/","", $telefone);
        switch(strlen($digits)) {
            case 13; $mask = '+%s%s (%s%s) %s %s%s%s%s-%s%s%s%s'; break;
            case 12; $mask = '(%s%s) %s %s%s%s%s-%s%s%s%s'; break;
            case 11; $mask = '(%s%s) %s %s%s%s%s-%s%s%s%s'; break;
            case 10; $mask = '(%s%s) %s%s%s%s-%s%s%s%s'; break;
            case 9; $mask = '%s %s%s%s%s-%s%s%s%s'; break;
            case 8; $mask = '%s%s%s%s-%s%s%s%s'; break;
            case 7; $mask = '%s%s%s-%s%s%s%s'; break;
        }
        return isset($mask) ? vsprintf($mask, str_split($digits)) : $digits;
    }

    public static function obfuscate_email($email, $encode = 1, $reverse = 0, $before = '<span class="email">', $after = '</span>') {
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
       

    public static function clearCNPJ($string) {
        $string = str_replace('.', '', $string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = str_replace('_', '', $string);
        $string = str_replace('/', '', $string);
        return $string;
    }   

    public static function clearCPF($string) {
        $string = str_replace('.', '', $string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = str_replace('_', '', $string);
        $string = str_replace('/', '', $string);
        return $string;
    }   

    public static function isCPF(string $string): bool {
        $string = trim($string);
        $string = self::clearCPF($string);
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
        if(!is_dir($path)) throw new \Exception("O caminho $path chamado por Strings::pathSanatize nao e um diretorio valido");
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
    

    function formatar_tempo($timeBD) {

        $timeNow = time();
        $timeRes = $timeNow - $timeBD;
        $nar = 0;
        
        // variável de retorno
        $r = "";
    
        // Agora
        if ($timeRes == 0){
            $r = "agora";
        } else
        // Segundos
        if ($timeRes > 0 and $timeRes < 60){
            $r = $timeRes. " segundos atr&aacute;s";
        } else
        // Minutos
        if (($timeRes > 59) and ($timeRes < 3599)){
            $timeRes = $timeRes / 60;	
            if (round($timeRes,$nar) >= 1 and round($timeRes,$nar) < 2){
                $r = round($timeRes,$nar). " minuto atr&aacute;s";
            } else {
                $r = round($timeRes,$nar). " minutos atr&aacute;s";
            }
        }
         else
        // Horas
        // Usar expressao regular para fazer hora e MEIA
        if ($timeRes > 3559 and $timeRes < 85399){
            $timeRes = $timeRes / 3600;
            
            if (round($timeRes,$nar) >= 1 and round($timeRes,$nar) < 2){
                $r = round($timeRes,$nar). " hora atr&aacute;s";
            }
            else {
                $r = round($timeRes,$nar). " horas atr&aacute;s";		
            }
        } else
        // Dias
        // Usar expressao regular para fazer dia e MEIO
        if ($timeRes > 86400 and $timeRes < 2591999){
            
            $timeRes = $timeRes / 86400;
            if (round($timeRes,$nar) >= 1 and round($timeRes,$nar) < 2){
                $r = round($timeRes,$nar). " dia atr&aacute;s";
            } else {
    
                preg_match('/(\d*)\.(\d)/', $timeRes, $matches);
                
                if ($matches[2] >= 5) {
                    $ext = round($timeRes,$nar) - 1;
                    
                    // Imprime o dia
                    $r = $ext;
                    
                    // Formata o dia, singular ou plural
                    if ($ext >= 1 and $ext < 2){ $r.= " dia "; } else { $r.= " dias ";}
                    
                    // Imprime o final da data
                    $r.= "&frac12; atr&aacute;s";
                    
                    
                } else {
                    $r = round($timeRes,0) . " dias atr&aacute;s";
                }
                
            }		
                    
        } else
        // Meses
        if ($timeRes > 2592000 and $timeRes < 31103999){
    
            $timeRes = $timeRes / 2592000;
            if (round($timeRes,$nar) >= 1 and round($timeRes,$nar) < 2){
                $r = round($timeRes,$nar). " mes atr&aacute;s";
            } else {
    
                preg_match('/(\d*)\.(\d)/', $timeRes, $matches);
                
                if ($matches[2] >= 5){
                    $ext = round($timeRes,$nar) - 1;
                    
                    // Imprime o mes
                    $r.= $ext;
                    
                    // Formata o mes, singular ou plural
                    if ($ext >= 1 and $ext < 2){ $r.= " mes "; } else { $r.= " meses ";}
                    
                    // Imprime o final da data
                    $r.= "&frac12; atr&aacute;s";
                } else {
                    $r = round($timeRes,0) . " meses atr&aacute;s";
                }
                
            }
        } else
        // Anos
        if ($timeRes > 31104000 and $timeRes < 155519999){
            
            $timeRes /= 31104000;
            if (round($timeRes,$nar) >= 1 and round($timeRes,$nar) < 2){
                $r = round($timeRes,$nar). " ano atr&aacute;s";
            } else {
                $r = round($timeRes,$nar). " anos atr&aacute;s";
            }
        } else
        // 5 anos, mostra data
        if ($timeRes > 155520000){
            
            $localTimeRes = localtime($timeRes);
            $localTimeNow = localtime(time());
                    
            $timeRes /= 31104000;
            $gmt = array();
            $gmt['mes'] = $localTimeRes[4];
            $gmt['ano'] = round($localTimeNow[5] + 1900 - $timeRes,0);				
                        
            $mon = array("Jan ","Fev ","Mar ","Abr ","Mai ","Jun ","Jul ","Ago ","Set ","Out ","Nov ","Dez "); 
            
            $r = $mon[$gmt['mes']] . $gmt['ano'];
        }
        
        return $r;
    
    }
    
	
	public function tempo($int, $tags = "atras") {
		$string = "";
		$tags = explode('|', $tags);
		foreach($tags as $tag){
			if($tag == 'atras') 		$string = Tempo::formatar_tempo($int);
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
	public static function geolocate_getestados($invert = false) {
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
	
	public static function geolocate_uf2estado($uf = null) {
		$uf = (is_null($uf)) ? $_REQUEST['str'] : $uf;
		$array = self::geolocate_getestados();
		return $array[strtoupper($uf)];
	}
	
	public static function geolocate_estado2uf($estado = null) {
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
	public static function string_format($string, $tags = 'strip_tags|nl2br|stripslashes') {
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
	
	public static function br2nl($string) {
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
                return number_format(self::int_format($int, 'intval'), 2, ',', '.');
            }
            if($tag == 'moedafloat') {
                return number_format(self::int_format($int, 'intval'), 2, '.', '');
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

  public static function sef(string $string):string {
    $string = self::latin2unicode($string);
    $string = self::fix_encoding($string);
    $string = self::remove_special_chars($string);
    $string = preg_replace('/[^A-Za-z0-9\-_\.,]/', '', $string);
    $string = self::latin2unicode($string);
    $string = str_replace(',', '', $string);
    $string = str_replace('.', '-', $string);
    $string = str_replace('---', '-', $string);
    $string = str_replace('--', '-', $string);
    $string = strtolower($string);
    return $string;
  }

  public static function file(string $string):string {
    $string = self::latin2unicode($string);
    $string = self::fix_encoding($string);
    $string = self::remove_special_chars($string);
    //remove all special characters
    $string = preg_replace('/[^A-Za-z0-9\-_\.,]/', '', $string);
    $string = self::latin2unicode($string);
    $string = strtolower($string);
    return $string;
  }

  public static function safename(string $string):string {
    $string = self::latin2unicode($string);
    $string = self::fix_encoding($string);
    $string = self::remove_special_chars($string);
    //remove all special characters
    $string = preg_replace('/[^A-Za-z0-9_]/', '', $string);
    //$string = str_replace('-', '_', $string);
    $string = self::latin2unicode($string);
    $string = strtolower($string);
    return $string;
  }

  public static function fix_encoding(string $string):string {
    if(mb_detect_encoding($string) == 'UTF-8') {
      return $string;
    } else
    if(mb_detect_encoding($string) == 'ASCII') {
      return self::convert_encoding($string, 'ASCII', 'UTF-8');
    } else 
    if(mb_detect_encoding($string) == 'ISO-8859-1') {
      return self::utf8_decode($string);
    }
    return self::utf8_encode($string);
  }

  public static function remove_special_chars(string $string):string {
    $special_characters = '[\W_]';
    $string = preg_replace($special_characters, '', $string);
    return $string;
  }

  private static function latin2unicode($string) {
    // Create a dictionary that maps Latin characters to their Unicode equivalents.
    $latin_to_unicode = [
    ' ' => '-',
    'À' => 'A', 
    'Á' => 'A', 
    'á' => 'a', 
    'Â' => 'A', 
    'Æ' => 'A', 
    'Ç' => 'C', 
    'È' => 'E', 
    'Ê' => 'E', 
    'Ë' => 'E', 
    'Í' => 'I', 
    'Î' => 'I', 
    'Ï' => 'I', 
    'Ñ' => 'N', 
    'Ó' => 'O', 
    'Ô' => 'O', 
    'Œ' => 'A', 
    'Ù' => 'U', 
    'Û' => 'U', 
    'Ü' => 'U', 
    'Ý' => 'Y', 
    'Þ' => 'P', 
    'ß' => 'B', 
    'ã' => 'a',
    'à' => 'a',
    'â' => 'a',
    'æ' => 'a',
    'ç' => 'c',
    'è' => 'e',
    'ê' => 'e',
    'ë' => 'e',
    'í' => 'i',
    'î' => 'i',
    'ï' => 'i',
    'ñ' => 'n',
    'ó' => 'o',
    'ô' => 'o',
    'œ' => 'a',
    'ù' => 'u',
    'û' => 'u',
    'ü' => 'u',
    'ý' => 'y',
    'þ' => 'p',
    'ß' => 'b',     
    'Ā' => 'A',
    'Ă' => 'A',
    'Ą' => 'A',
    'Ć' => 'C',
    'Ĉ' => 'C',
    'Ċ' => 'C',
    'Č' => 'C',
    'Ď' => 'D',
    'Đ' => 'D',
    'Ē' => 'E',
    'Ĕ' => 'E',
    'Ė' => 'E',
    'Ę' => 'E',
    'Ě' => 'E',
    'Ĝ' => 'G',
    'Ġ' => 'G',
    'Ģ' => 'G',
    'Ĥ' => 'H',
    'Ĩ' => 'I',
    'Ī' => 'I',
    'Ĭ' => 'I',
    'Į' => 'I',
    'İ' => 'I',
    'Ĵ' => 'J',
    'Ķ' => 'K',
    'Ĺ' => 'L',
    'Ļ' => 'L',
    'Ľ' => 'L',
    'Ń' => 'N',
    'Ņ' => 'N',
    'Ň' => 'N',
    'Ō' => 'O',
    'Ŏ' => 'O',
    'Ő' => 'O',
    'Œ' => 'O',
    'Ŕ' => 'R',
    'Ŗ' => 'R',
    'Ř' => 'R',
    'Ś' => 'S',
    'Ŝ' => 'S',
    'Ş' => 'S',
    'Ť' => 'T',
    'Ţ' => 'T',
    'Ŧ' => 'T',
    'Ũ' => 'U',
    'Ū' => 'U',
    'Ŭ' => 'U',
    'Ů' => 'U',
    'Ü' => 'U',
    'Ý' => 'Y',
    'Ÿ' => 'Y',
    'Ź' => 'Z',
    'Ż' => 'Z',
    'Ž' => 'Z',
    'ă' => 'a',
    'ą' => 'a',
    'ć' => 'c',
    'ĉ' => 'c',
    'ċ' => 'c',
    'č' => 'c',
    'ď' => 'd',
    'đ' => 'd',
    'ē' => 'e',
    'ĕ' => 'e',
    'ė' => 'e',
    'ę' => 'e',
    'ě' => 'e',
    'ĝ' => 'g',
    'ġ' => 'g',
    'ģ' => 'g',
    'ĥ' => 'h',
    'ĩ' => 'i',
    'ī' => 'i',
    'ĭ' => 'i',
    'į' => 'i',
    'ı' => 'i',
    'ĵ' => 'j',
    'ķ' => 'k',
    'ĺ' => 'l',
    'ļ' => 'l',
    'ľ' => 'l',
    'ń' => 'n',
    'ņ' => 'n',
    'ň' => 'n',
    'ō' => 'o',
    'ŏ' => 'o',
    'ő' => 'o',
    'œ' => 'o',
    'ŕ' => 'r',
    'ŗ' => 'r',
    'ř' => 'r',
    'ś' => 's',
    'ŝ' => 's',
    'ş' => 's',
    'ť' => 't',
    'ţ' => 't',
    'ŧ' => 't',
    'ũ' => 'u',
    'ū' => 'u',
    'ŭ' => 'u',
    'ů' => 'u',
    'ü' => 'u',
    'ý' => 'y',
    'ÿ' => 'y',
    'ź' => 'z',
    'ż' => 'z',
    'ž' => 'z',
    ];   

    // Replace all Latin characters with their Unicode equivalents.
    foreach ($latin_to_unicode as $latin_char => $unicode_char) {
      $string = str_replace($latin_char, $unicode_char, $string);
    }        

    return $string;
  }

  public static function utf8_encode($string) {
    return self::convert_encoding($string, 'UTF-8', 'ISO-8859-1');
  }

  public static function utf8_decode($string) {
    return self::convert_encoding($string, 'ISO-8859-1', 'UTF-8');
  }

  /**
   * @function convert_encoding
   * @param string $string
   * @param string $to can be 'Windows-1252' or 'ISO-8859-1'
   * @return string $converted_string
   */
  private static function convert_encoding(string $string, $from ='UTF-8', $to = 'ISO-8859-1'):string {
    $utf8_string = mb_convert_encoding($string, $from, $to);
    return $utf8_string;
  }

  public static function contarAcentos(string $string):int {
    // Expressão regular para caracteres acentuados (acentos e diacríticos)
    $acentosPattern = '/[À-ÖØ-öø-ÿĀ-ž]/u';

    // Contar caracteres acentuados
    preg_match_all($acentosPattern, $string, $matchesAcentos);
    $quantidadeAcentos = count($matchesAcentos[0]);

    return $quantidadeAcentos;
  }

  public static function contarCaracteresEspeciais(string $string):int {
    // Expressão regular para caracteres especiais (tudo que não é alfanumérico)
    $caracteresEspeciaisPattern = '/[^a-zA-Z0-9\s]/u';

    // Contar caracteres especiais
    preg_match_all($caracteresEspeciaisPattern, $string, $matchesEspeciais);
    $quantidadeCaracteresEspeciais = count($matchesEspeciais[0]);

    return  $quantidadeCaracteresEspeciais;
  }

  public static function contarAcentosECaracteresEspeciais(string $string):int {
    return self::contarAcentos($string) + self::contarCaracteresEspeciais($string);
  }

  public static function removerFrasesDuplicadas(string $texto):string {
    // Divide o texto em frases usando delimitadores comuns de frases
    $frases = preg_split('/(\.|\!|\?|,|;)+\s*/', $texto, -1, PREG_SPLIT_NO_EMPTY);

    $frasesUnicas = [];
    $frasesVistas = [];

    foreach ($frases as $frase) {
        // Remove espaços extras antes de verificar duplicatas
        $frase = trim($frase);

        // Se a frase ainda não foi vista, adicione-a à lista de frases únicas
        if (!in_array($frase, $frasesVistas)) {
            $frasesUnicas[] = $frase;
            $frasesVistas[] = $frase; // Marca a frase como vista
        }
    }

    // Reconstroi o texto original com frases únicas
    return implode('. ', $frasesUnicas) . '.';
  }

  public static function shrink(string $string, int $length = 100, string $append = '...'):string {
    if(strlen($string) > $length) {
      $length = intval($length/2) - strlen($append);
      $stringStart = substr($string, 0, $length);
      $stringEnd = substr($string, -$length);
      $string = $stringStart.$append.$stringEnd;
    }
    return $string;
  }
}
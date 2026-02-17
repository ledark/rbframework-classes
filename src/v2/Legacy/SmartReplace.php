<?php

namespace RBFrameworks\Core\Legacy;

use RBFrameworks\Core\Utils\Encoding;

class SmartReplace
{
    /*
função smart_replace(conteudo:[string], [dados:array], [literal:bool])
	versão: 1.5
	Autor: Ricardo [at] Bermejo.com.br

	Changelog:
	1.5 - Adicionado a função smart_replaceloop
	1.4 - Em 03/10/2017 foi adicionada a função smart_replacef, onde o parâmetro $string se torna o nome de um arquivo para fazer um include e replace ao mesmo tempo.
	1.3 - Agora o parâmetro $string é opcional. Quando não informado, ele irá assumir um ob_get_clean();
	1.2 - Adicionado o terceiro parâmetro $returnLiteral: um valor booleano true ou false, para mostrar as chaves e o nome da variável quando a mesma não existir. Padrão = true;

	Explicação:
	$string: html ou qualquer texto que contenha {variaveis} ou {palavras} entre {chaves}
	$array: uma array opcional, que contenha a seguitne estrutura: 
	$array = array(
		'variaveis' => 'substituirá a palavra variaveis'
	,	'palavras' => 'substituirá a palavra palavras'
	,	'chaves' => 'substituirá a palavra chaves'
	);
	E o valor retornado será a string com seus respectivos valores substituídos. Ele tentará substituir valores da array ou do GLOBALS do php
	
	Exemplo:
	<? ob_start(); ?>
	{variavel1} {variavel2}
	<?
	echo smart_replace();
*/
    public static function smart_replaceloop($file, $array = array(), $returnLiteral = true)
    {

        $file_base = (substr($file, strlen($file) - 4, 1) == ".") ? substr($file, 0, -4) : substr($file, 0, -5);
        $file_config = $file_base . '.config.php';
        $file_head = $file_base . '.head.php';
        $file_loop = $file_base . '.loop.php';
        $file_foot = $file_base . '.foot.php';
        $file_single = $file_base . '.single.php';
        $file_none = $file_base . '.none.php';

        if (file_exists($file_config)) include($file_config);

        $string = file_get_contents($file);

        preg_match("/<header>(.*?)<\/header>/is", $string, $Matches);
        $head = $Matches[1];

        preg_match("/<looper>(.*?)<\/looper>/is", $string, $Matches);
        $loop = $Matches[1];

        preg_match("/<footer>(.*?)<\/footer>/is", $string, $Matches);
        $foot = $Matches[1];

        preg_match("/<none>(.*?)<\/none>/is", $string, $Matches);
        $none = $Matches[1];

        preg_match("/<single>(.*?)<\/single>/is", $string, $Matches);
        $once = $Matches[1];

        //Caso tag <none>
        if (!empty($none) and !count($array)) {
            if (file_exists($file_none)) include($file_none);
            return self::smart_replace($none, $array[0], $returnLiteral);
        }

        //Caso tag <single>
        if (!empty($once) and count($array) == 1) {
            if (file_exists($file_single)) include($file_single);
            return self::smart_replace($once, $array[0], $returnLiteral);
        }

        //Caso normal, com <header><looper><footer>
        $buffer = "";
        if (file_exists($file_head)) include($file_head);
        $buffer .= self::smart_replace($head, reset($array), $returnLiteral);
        foreach ($array as $i => $r) {
            if (file_exists($file_loop)) include($file_loop);
            $buffer .= self::smart_replace($loop, $r, $returnLiteral);
        }
        if (file_exists($file_foot)) include($file_foot);
        $buffer .= self::smart_replace($foot, end($array), $returnLiteral);
        return $buffer;
    }
    public static function smart_replacef($file, $array = array(), $returnLiteral = true)
    {
        if (!file_exists($file)) return "$file não existe";
        $string = file_get_contents($file);
        return self::smart_replace($string, $array, $returnLiteral);
    }


    public static function smart_replace($string = null, $array = array(), $returnLiteral = true, $encoding = '')
    {
        global $dic, $RBsmart_replace_returnLiteral;
        $RBsmart_replace_returnLiteral = $returnLiteral;
        if (is_null($string)) $string = ob_get_clean();

        if (is_array($dic)) {
            $array = @array_merge($array, $dic);
        }

        if (is_array($array)) {
            foreach ($array as $chave => $valor) {
                if (is_string($chave)) {
                    if (is_integer($valor)) $valor = (string) $valor;
                    if (is_string($valor)) {
                        $string = str_replace('{' . $chave . '}', $valor, $string);
                    }
                }
            }
        }

        if ($returnLiteral) {
            if (version_compare(phpversion(), '7.4.0', '<')) {
                $string = @preg_replace_callback('/{(\w+)}/', 'smart_replace_matches', $string);
            } else {
                $string = @preg_replace_callback('/{(\w+)}/', function($var){
                    global $RBsmart_replace_returnLiteral;
                    if (isset($GLOBALS[end($var)])) {
                        return $GLOBALS[end($var)];
                    } else 
                    if (strpos($var[count($var) - 1], '|') !== false) {
                        $var = explode('|', end($var));
                        if (isset($GLOBALS[$var[0]])) {
                            return $GLOBALS[$var[0]];
                        } else {
                            if ($var[1] == 'NULL') $GLOBALS[$var[0]] = '';
                            if ($var[1] == 'uniqid()') $GLOBALS[$var[0]] = uniqid();
                            if (!isset($GLOBALS[$var[0]])) $GLOBALS[$var[0]] = $var[1];
                            return $GLOBALS[$var[0]];
                        }
                    } else {
                        if ($RBsmart_replace_returnLiteral)
                            return '{' . end($var) . '}';
                    }
                }, $string);
            }            
        }


        $string = preg_replace_callback('/{(\w+)}/', function ($var) use (&$array) {
            //$string = preg_replace_callback('/{(.[^}]+)}/', function($var) use (&$array) {

            global $RBsmart_replace_returnLiteral;
            if (isset($GLOBALS[end($var)])) {
                return $GLOBALS[end($var)];
            } else 
        if (strpos($var[count($var) - 1], '|') !== false) {
                $var = explode('|', end($var));
                if (isset($array[$var[0]])) return $array[$var[0]];
                if (isset($GLOBALS[$var[0]])) {
                    return $GLOBALS[$var[0]];
                } else {
                    if ($var[1] == 'NULL') $array[$var[0]] = '';
                    if ($var[1] == 'uniqid()') $array[$var[0]] = uniqid();
                    if (!isset($array[$var[0]])) {
                        return $array[$var[0]];
                    }
                    if (!isset($GLOBALS[$var[0]])) $GLOBALS[$var[0]] = $var[1];
                    return $GLOBALS[$var[0]];
                }
            } else {
                if ($RBsmart_replace_returnLiteral)
                    return '{' . end($var) . '}';
            }
        }, $string);

        //Adicionado em 20/08/2019
        if (isset($encoding)) {
            switch (str_replace('-', '', strtolower($encoding))) {
                case 'utf8_encode':
                    $string = Encoding::DeepEncode($string);
                    break;
                case 'utf8_decode':
                    $string = Encoding::DeepDecode($string);
                    break;
                case 'utf8_fix':
                    $string = Encoding::fixUTF8($string);
                    break;
                case 'to_utf8':
                    $string = Encoding::toUTF8($string);
                    break;
                case 'to_latin':
                    $string = Encoding::toLatin1($string);
                    break;
                case 'to_iso8859':
                    $string = Encoding::toISO8859($string);
                    break;
                case 'to_iso88591':
                    $string = Encoding::toISO8859($string);
                    break;
            }
        }
        return $string;
    }

    /*
function smart_replace($string = null, $array = array(), $returnLiteral = true) {
	global $dic, $RBsmart_replace_returnLiteral;
	$RBsmart_replace_returnLiteral = $returnLiteral;
	if(is_null($string)) $string = ob_get_clean();

	if(is_array($dic)) {
		$array = @array_merge($array, $dic);
	}
    if(is_array($array)) {
        foreach($array as $chave => $valor) {
            if(is_string($chave)) {
                $string = str_replace('{'.$chave.'}', $valor, $string);
            }
        }
    }
	
	$string = preg_replace_callback('/{(\w+)}/', 'smart_replace_matches', $string);
	return $string;
}
*/
    public static function smart_replace_matches($var)
    {
        global $RBsmart_replace_returnLiteral;
        if (isset($GLOBALS[end($var)])) {
            return $GLOBALS[end($var)];
        } else 
    if (strpos($var[count($var) - 1], '|') !== false) {
            $var = explode('|', end($var));
            if (isset($GLOBALS[$var[0]])) {
                return $GLOBALS[$var[0]];
            } else {
                if ($var[1] == 'NULL') $GLOBALS[$var[0]] = '';
                if ($var[1] == 'uniqid()') $GLOBALS[$var[0]] = uniqid();
                if (!isset($GLOBALS[$var[0]])) $GLOBALS[$var[0]] = $var[1];
                return $GLOBALS[$var[0]];
            }
        } else {
            if ($RBsmart_replace_returnLiteral)
                return '{' . end($var) . '}';
        }
    }
}

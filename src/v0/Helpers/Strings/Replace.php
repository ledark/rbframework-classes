<?php

namespace RBFrameworks\Helpers\Strings;

/*
 * Exemplo:
 * 
 * $saida_html = "conteudo em html qualquer...";
 * 
 * $Output = Replace::replace($saida_html, [])
 * 
 */


class Replace {
    
    private $string;
    private $replaces = [];
    private $backets = ['{', '}'];
    private $inputEncoding = 'ISO8859-1';
    private $outputEncoding = 'utf-8';
    private $returnLiteral = true;
    
    protected $pattern = '/{(\w+)}/';


    public function __construct(string $string = "") {
        $this->string = $string;
    }
    
    public function setString(string $string) {
        $this->string = $string;
        return $this;
    }
    
    public function getString() {
        return $this->string;
    }
    
    public function getReplaces():array {
        return $this->replaces;
    }
    
    public function setReplaces(array $replaces) {
        $this->replaces = array_merge($this->replaces, $replaces);
        return $this;
    }
    
    public function setBrackets(array $brackets) {
        $this->backets = $brackets;
        return $this;
    }
    
    public function setReturnLiteral(bool $active) {
        $this->returnLiteral = $active;
        return $this;
    }
    
    public function inputEncoding(array $encoding) {
        $this->inputEncoding = $encoding;
        return $this;
    }

    public function outputEncoding(array $encoding) {
        $this->outputEncoding = $encoding;
        return $this;
    }    
    
    public static function replace(string $string, array $replaces, $returnLiteral = true, $encoding = '') {
        $replacer = new Replace($string);
        $replacer
            ->setReplaces($replaces)
            ->setBrackets(['{', '}'])
            ->setReturnLiteral($returnLiteral)
            ->inputEncoding($encoding)
            ->outputEncoding($encoding)
        ;
    }
    
    public function simpleReplace() {
        foreach($this->getReplaces() as $chave => $valor) {
            if(is_string($chave)) {
                if(is_string($valor)) {
                    $this->setString(str_replace('{'.$chave.'}', $valor, $this->getString()));
                }
            }
        }        
    }
    
    public function run() {
        preg_replace_callback($this->pattern, $this->replaceMatches(), $this->getString());
    }
    
    public function replaceMatches() {
        if(isset($GLOBALS[end($var)])) {
            return $GLOBALS[end($var)];
        } else 
        if( strpos( $var[count($var)-1] , '|') !== false) {
            $var = explode('|', end($var));
            if(isset($GLOBALS[$var[0]])) {
                return $GLOBALS[$var[0]];
            } else {
                return $var[1];
            }

        } else {
            if($this->returnLiteral)
            return '{'.end($var).'}';
        }
    }
    

}
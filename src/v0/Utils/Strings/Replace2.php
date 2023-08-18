<?php

namespace RBFrameworks\Utils\Strings;

/*
 * Exemplo:
 * 
 * $saida_html = "conteudo em html qualquer...";
 * 
 * $Output = Replace::replace($saida_html, [])
 * 
 */


class Replace2 {
    
    private $string;
    private $replaces = [];
    private $backets = ['{', '}'];
    private $inputEncoding = '';
    private $outputEncoding = '';
    
    protected $renderedContent = '';

    public function __construct(string $string = "", array $replaces = []) {
        $this->setString($string);
        $this->setReplaces($replaces);
    }

    public static function replace(string $string = null, array $replaces = [], bool $returnLiteral = true):string {
        if(is_null($string)) $string = ob_get_clean();
        if($returnLiteral) {
            return (new self($string, $replaces))->ignoreLiteral()->render(true);
        } else {
            return (new self($string, $replaces))->whenLiteral( function($matches) { return ''.$matches[1].''; } )->render(true);
        }
    }

    /**
     * @sample: 
     *  $string = "choveu {ontem}";
     *  $replace = (new Replace($string))->ignoreLiteral()->render(); //mostrar? choveu {ontem}
     *  $replace = (new Replace($string))->whenLiteral( funcion($matches) { return '--['.$matches[1].']--'; } )->render(); //mostrar? choveu --[ontem]--
     */
    public $events = [
        'whenLiteral' => null,
        'beforeRender' => null,
    ];

    public function addEvent(string $eventName, callable $callback = null) {
        $this->events[$eventName] = $callback;
        return $this;
    }


    //M?todo Facade para whenLiteral
    public function ignoreLiteral():object {
        $this->whenLiteral(function($matches) {
            return $matches[1];
        });
        return $this;
    }

    //M?todo Facade para whenLiteral
    public function useLiteral():object {
        $this->whenLiteral(function($matches) {
            return $matches[0];
        });
        return $this;
    }
    
    //Callback para quando o retorno deveria ser um Literal
    public function whenLiteral(callable $callable):object {
        $this->events['whenLiteral'] = $callable;
        return $this;
    }
    
    private function matchLiteral($matches) {
        return is_null($this->events['whenLiteral']) ? $matches[0] : $this->events['whenLiteral']($matches);
    }
    
    //Handle: String
    public function setString(string $string) {
        $this->string = $string;
        return $this;
    }
    
    public function getString() {
        $content = $this->string;
        if( $this->inputEncoding() == 'utf8') $content = utf8_decode($content);
        if( $this->inputEncoding() == 'iso88591') $content = utf8_encode($content);        
        return $content;
    }
    
    //Handle: Replaces    
    public function setReplaces(array $replaces) {
        $this->replaces = array_merge($this->replaces, $replaces);
        return $this;
    }

    public function addReplace(string $name, string $value):object {
        $this->replaces[$name] = $value;
        return $this;
    }

    public function getReplaces():array {
        return $this->replaces;
    }
    
    public function setBrackets(array $brackets) {
        $this->backets = $brackets;
        return $this;
    }

    public function getBrackets() {
        return $this->backets;
    }

    public $pattern = '(\w+)';

    public function setPattern(string $pattern) {
        $this->pattern = $pattern;
        return $this;
    }

    public function getPattern():string {
        return '/'.$this->getBrackets()[0].$this->pattern.$this->getBrackets()[1].'/';
    }

    public function replaceMatches(array $matches):string {
        if(in_array($matches[1], array_keys($this->getReplaces()))) {
            $res = $this->getReplaces()[$matches[1]];
            return is_string($res) ? $res : "";
        }
        return $this->matchLiteral($matches);
        //return ($this->returnLiteral) ? $matches[0] : $matches[1];
    }

    //HandleEncoding
    public function inputEncoding(string $encoding = '') {
        if(empty($encoding)) return strtolower( str_replace('-', '', $this->inputEncoding) );
        $this->inputEncoding = $encoding;
        return $this;
    }

    public function outputEncoding(string $encoding = '') {
        if(empty($encoding)) return strtolower( str_replace('-', '', $this->outputEncoding) );
        $this->outputEncoding = $encoding;
        return $this;
    } 


    //Fun??es de Renderiza??o
    private function internalRender():string {
        if(empty($this->renderedContent)) {
            $this->renderedContent = preg_replace_callback($this->getPattern(), function($matches){ return $this->replaceMatches($matches); }, $this->getString());
        }
        if(is_callable($this->events['beforeRender'])) $this->renderedContent = $this->events['beforeRender']($this->renderedContent);
        return $this->renderedContent;
    }

    private function getRendered():string {
        $content = $this->internalRender();
        if( $this->outputEncoding() == 'utf8') $content = utf8_encode($content);
        if( $this->outputEncoding() == 'iso88591') $content = utf8_decode($content);
        return $content;
    }

    //Devolu??o do Conte?do ap?s Renderizado
    public function render(bool $capture = false) {
        extract($this->getReplaces());
        if($capture) return $this->getRendered(); else echo $this->getRendered();
    }

    public function __toString() {
        return $this->render(true);
    }

    public static function get(string $content = null, array $replaces = []) {
        return (new self($content, $replaces))->render(true);
    }
    

}
<?php 

namespace RBFrameworks\Core\Types;

class Pattern {

    private $pattern = '';

    public function __construct($mixed) {

        $this->setPattern( is_string($mixed) ? $mixed : '' );
        if($mixed instanceof \ReflectionParameter) $this->generateFromReflectionParameter($mixed);
    }

    private function generateFromReflectionParameter(\ReflectionParameter $parameter) {
        $str = $parameter->hasType() ? $parameter->getType() : 'mixed';
        if(!is_string($str)) $str = $str->getName();
        switch($str) {
            case 'string':
                $this->setPattern( '/'.self::word() );
            break;
            case 'int':
                $this->setPattern( '/'.self::number() );
            break;
            case 'mixed':
                $this->setPattern( '/'.self::any() );
            break;
        }
    }

    public function setPattern(string $pattern) {
        $this->pattern = $pattern;
    }

    public function getPattern():string {
        return $this->pattern;
    }

    public static function number(array $capsule = ['(', ')']):string { return $capsule[0].'\d+'.$capsule[1]; }
    public static function word(array $capsule = ['(', ')']):string { return $capsule[0].'\w+'.$capsule[1]; }
    public static function sef(array $capsule = ['(', ')']):string { return $capsule[0].'[a-z0-9_-]+'.$capsule[1]; }
    public static function any(array $capsule = ['(', ')']):string { return $capsule[0].'[^/]+'.$capsule[1]; }

}
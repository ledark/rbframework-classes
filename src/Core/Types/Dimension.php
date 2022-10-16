<?php 

namespace RBFrameworks\Core\Types;

class Dimension
{
    public $_value;
    private $un = '';

    
    public function __construct($value)
    {
        //if(is_float($value)) $value = round($value);

        $this->_value = (string) $value;
        $this->detectUn();
        $this->detectFloat();
    }

    public function setUn(string $un):object {
        $this->un = $un;
        return $this;
    }
    
    public function asMm():object {
        $this->setUn('mm');
        return $this;
    }

    public function asCm():object {
        $this->setUn('cm');
        return $this;
    }

    public function asM():object {
        $this->setUn('m');
        return $this;
    }

    public function asKm():object {
        $this->setUn('km');
        return $this;
    }

    public function asMilimiter():object {
        $this->setUn('mm');
        return $this;
    }

    public function asCentimeter():object {
        $this->setUn('cm');
        return $this;
    }

    public function asMeter():object {
        $this->setUn('m');
        return $this;
    }

    public function asKilometer():object {
        $this->setUn('km');
        return $this;
    }

    private function detectUn() {
        if(strpos(strtolower($this->_value), 'mm') !== false) {
            if(empty($this->un)) $this->un = 'mm';
        } 
        if(strpos(strtolower($this->_value), 'cm') !== false) {
            if(empty($this->un)) $this->un = 'cm';
        } 
        if(strpos(strtolower($this->_value), 'km') !== false) {
            if(empty($this->un)) $this->un = 'km';
        } 
        if(strpos(strtolower($this->_value), 'm') !== false) {
            if(empty($this->un)) $this->un = 'm';
        } 
        if(empty($this->un)) $this->un = 'cm';
    }

    private function detectFloat() {
        $float_point = $this->getNumber();
        if(strpos($this->_value, '.') !== false) {
            $point = explode('.', $this->_value, 2);
            $point[0] = preg_replace('/[^(\d+)]/m', '', $point[0]);
            $point[1] = preg_replace('/[^(\d+)]/m', '', $point[1]);
            $float_point = $point[0].'.'.$point[1];
        }
        switch($this->getUn()) {
            case 'km':
                $float_point = $float_point*1000*1000;
                $this->_value = floor($float_point);
            break;
            case 'm':
                $float_point = $float_point*1000;
                $this->_value = floor($float_point);
            break;
            case 'cm':
                $float_point = $float_point*10;
                $this->_value = ceil($float_point);
            break;                
            case 'mm':
                $this->_value = ceil($float_point);
            break;
        }
        $this->_value = (string) $this->_value;        
    }

    public function getUn():string {
        return $this->un;
    }

    public function getNumber():int {
        if(!isset($this->_number)) {
            $this->_number = preg_replace('/[^(\d+)]/m', '', intval($this->_value));
            $this->_number = intval($this->_number);
        }
        return $this->_number;
    }

    public function getFloat():float {
        return (float) $this->_value;
    }

    public function getFormatted(bool $spacing = false):string {
        return $this->getNumber().( $spacing ? ' ' : '' ).$this->getUn();
    }

    public function getMilimeter():float {
        return $this->getConverted('mm');
        //return $this->getFloat();
        //$n = (float) $this->_value;
        //return intval($n);
    }
    public function getCentimeter():float {
        return $this->getConverted('cm');
        //return ceil($this->getMilimeter()/10);
    }
    public function getMeter():float {
        return $this->getConverted('m');
        //return ceil($this->getMilimeter()/1000);
    }
    public function getKilometer():float {
        return $this->getConverted('km');
        //return ceil($this->getMeter()/1000);
    }

    //

    public function getConverted(string $to = 'mm'):float {
        $value = $this->getNumber();
        switch($this->getUn()) {
            case 'km':
                switch($to) {
                    case 'km':
                        return $value;
                    break;
                    case 'm':
                        return $value*1000;
                    break;
                    case 'cm':
                        return $value*100000;
                    break;                    
                    case 'mm':
                        return $value*1e+6;
                    break;
                }
            break;
            case 'm':
                switch($to) {
                    case 'km':
                        return $value/1000;
                    break;
                    case 'm':
                        return $value;
                    break;
                    case 'cm':
                        return $value*100;
                    break;                    
                    case 'mm':
                        return $value*1000;
                    break;
                }
            break;
            case 'cm':
                switch($to) {
                    case 'km':
                        return $value/100000;
                    break;
                    case 'm':
                        return $value/100;
                    break;
                    case 'cm':
                        return $value;
                    break;                    
                    case 'mm':
                        return $value*10;
                    break;
                }
            break;                
            case 'mm':
                switch($to) {
                    case 'km':
                        return $value/1e+6;
                    break;
                    case 'm':
                        return $value/1000;
                    break;
                    case 'cm':
                        return $value/10;
                    break;                    
                    case 'mm':
                        return $value;
                    break;
                }
            break;
        }
    }
    

}


<?php 

namespace RBFrameworks\Core\Types;

class Dimension
{
    public $_value;
    private $un = '';

    
    public function __construct($value)
    {
        if(is_float($value)) $value = round($value);

        $this->_value = (string) $value;
        $this->detectUn();
        $this->detectFloat();
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

    public function getMilimeter():int {
        $n = (float) $this->_value;
        return intval($n);
    }
    public function getCentimeter():int {
        return ceil($this->getMilimeter()/10);
    }
    public function getMeter():int {
        return ceil($this->getMilimeter()/1000);
    }
    public function getKilometer():int {
        return ceil($this->getMeter()/1000);
    }

}


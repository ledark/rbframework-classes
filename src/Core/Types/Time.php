<?php 

namespace RBFrameworks\Core\Types;

class Time implements TypeInterface {

    private $_value;
    private $originalValue;
    private ?string $timetype;
    private ?string $formattype;
    
    public const NOT_A_DATE = 'NaD';
    public const NOT_A_TIME = 'NaT';
    public const NOT_A_DATE_OR_TIME = 'NaDT';

    public const IS_DATE = 'asDate';
    public const IS_TIME = 'asTime';
    public const IS_DATETIME = 'asDateTime';

    public function getShrinked() {
        return $this->getValue();
    }
    public function getHydrated() {
        return $this->getValue();
    }

    //getFormatted usado para retornar o valor esperado após tratamento do Type
    public function getFormatted() {
        return $this->getValue();
    }

    //getNumber para extrair um valor numérico do Type
    public function getNumber():int {
        return $this->getValue();
    }

    //getString para extrair um valor string do Type
    public function getString():string {
        return $this->getValue();
    }


    /**
     * $datastr pode ser qualquer valor;
     * $timetype tentará obter o tipo automaticamente: [br, en, unix, NaN]
     */
    public function __construct( $datastr) {
        $this->originalValue = $datastr;
        $this->_value = $datastr;
        $this->timetype = null;
        $this->formattype = null;
    }

    public function getValue() {
        return $this->_value;
    }



    //Expected: Dates
    private static function getTypeOnDate( $datastr):string {
        if(strlen($datastr) == 10) {
            if(strpos($datastr, '/')) {
                return 'br';
            } else
            if(strpos($datastr, '-')) {
                return 'en';
            } else {
                return 'unix';
            }
        } else
        if(is_numeric($datastr)) {
            return 'unix';
        }
        return self::NOT_A_DATE;
    }

    //Expected: Times
    private static function getTypeOnTime( $datastr):string {
        return self::NOT_A_TIME;
    }

    //Expected: Date and Times
    private static function getTypeOnDateTime( $datastr):string {
        return self::NOT_A_DATE_OR_TIME;
    }

    public function getType():string {
        //WhenDefined
        if(!is_null($this->timetype)) return $this->timetype;
        

        //as:Date
        $this->formattype = self::IS_DATE;
        $this->timetype = self::getTypeOnDate($this->getValue());

        //as:Time
        if($this->timetype == self::NOT_A_DATE) {
            $this->formattype = self::IS_TIME;
            $this->timetype = self::getTypeOnTime($this->getValue());
        }

        //asDateTime
        if($this->timetype == self::NOT_A_TIME) {
            $this->formattype = self::IS_DATETIME;
            $this->timetype = self::getTypeOnDateTime($this->getValue());
        }
        
        //asInvalidFormat
        if($this->timetype == self::NOT_A_DATE_OR_TIME) {
            $this->formattype = self::IS_TIME;
            $this->timetype = self::NOT_A_DATE_OR_TIME;
        }

        return $this->timetype;
    }

    public function getFormatType():string {
        if(!is_null($this->formattype)) return $this->formattype;
        $this->getType();
        return $this->formattype;
    }

    /*
    private function decode($datastr):string {
        $datastr = trim($datastr);
        if(strlen($datastr) == 10) {
            if(strpos($datastr, '/')) {
                return 'br';
            } else
            if(strpos($datastr, '-')) {
                return 'en';
            } else {
                return 'unix';
            }
        } else
        if(is_numeric($datastr)) {
            return 'unix';
        }
        return 'NaN';
    }

    public function convertTo(string $type = 'unix') {
        $datastr = $this->_value;
        $para = $type;
        switch($this->timetype) {
            case 'br':
                switch($para) {
                    case 'en':
                        return $this->date_convert_br2en($datastr);
                    break;
                    case 'br':
                        return $this->date_convert_br2br($datastr);
                    break;
                    case 'unix':
                        return $this->date_convert_br2unix($datastr);
                    break;
                }
            break;
            case 'en':
                switch($para) {
                    case 'en':
                        return $this->date_convert_en2en($datastr);
                    break;
                    case 'br':
                        return $this->date_convert_en2br($datastr);
                    break;
                    case 'unix':
                        return $this->date_convert_en2unix($datastr);
                    break;
                }		
            break;
            case 'unix':
                switch($para) {
                    case 'en':
                        return $this->date_convert_unix2en($datastr);
                    break;
                    case 'br':
                        return $this->date_convert_unix2br($datastr);
                    break;
                    case 'unix':
                        return $this->date_convert_unix2unix($datastr);
                    break;
                }		
            break;
        }
    }

    //Convers?es BR
    public function date_convert_br2en($databr) {
        return substr($databr, 6,4).'-'.substr($databr, 3,2).'-'.substr($databr, 0,2);
    }
    public function date_convert_br2br($databr) {
        return $databr;
    }
    public function date_convert_br2unix($databr) {
        return strtotime($this->date_convert_br2en($databr));
    }

    //Convers?es EN
    public function date_convert_en2en($dataen) {
        return $dataen;
    }
    public function date_convert_en2br($dataen) {
        return substr($dataen, 8,2).'/'.substr($dataen, 5,2).'/'.substr($dataen, 0,4);
    }
    public function date_convert_en2unix($dataen) {
        return strtotime($dataen);
    }

    //Convers?es UNIX
    public function date_convert_unix2en($dataunix) {
        return date('Y-m-d', $dataunix);
    }
    public function date_convert_unix2br($dataunix) {
        return date('d/m/Y', $dataunix);
    }
    public function date_convert_unix2unix($dataunix) {
        return $dataunix;
    }
    

    public function getFormatted():string {
        return date_convert($this->_value, 'br');
    }   
    */ 

}
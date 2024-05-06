<?php

namespace RBFrameworks\Core\Types;

class Phone
{

    protected $_value;
    protected $number;

    public function __construct(string $value)
    {
        $this->_value = $this->validate($value);
    }

    private function validate(string $value): string
    {

        //BasicPreparation
        $value = trim($value);
        $value = preg_replace('/\D/', '', $value);

        //Validacoes Basicas
        if (!ctype_digit($value)) throw new \Exception("Telefone Inválido" . $value);

        //Outras Valida��es


        return $value;
    }

    public function getNumber(): int
    {
        if (!isset($this->number)) $this->number = preg_replace('/\D/', '', $this->_value);
        return (int) $this->number;
    }

    private function mask8($number): string
    {
        $string = "(XX) PIECEA-PIECEB";
        $string = str_replace('PIECEA',     substr($number, 0, 4), $string);
        $string = str_replace('PIECEB',     substr($number, 4, 4), $string);
        return $string;
    }
    private function mask9($number): string
    {
        $string = "(XX) PIECEA-PIECEB";
        $string = str_replace('PIECEA',     substr($number, 0, 5), $string);
        $string = str_replace('PIECEB',     substr($number, 5, 4), $string);
        return $string;
    }
    private function mask10($number): string
    {
        $string = "(DDD) PIECEA-PIECEB";
        $string = str_replace('DDD',        substr($number, 0, 2), $string);
        $string = str_replace('PIECEA',     substr($number, 2, 4), $string);
        $string = str_replace('PIECEB',     substr($number, 6, 4), $string);
        return $string;
    }
    private function mask11($number): string
    {
        $string = "(DDD) PIECEA-PIECEB";
        $string = str_replace('DDD',        substr($number, 0, 2), $string);
        $string = str_replace('PIECEA',     substr($number, 2, 5), $string);
        $string = str_replace('PIECEB',     substr($number, 7, 4), $string);
        return $string;
    }
    private function mask12($number): string
    {
        $string = "+NCODE (DDD) PIECEA-PIECEB";
        $string = str_replace('NCODE',      substr($number, 0, 2), $string);
        $string = str_replace('DDD',        substr($number, 2, 2), $string);
        $string = str_replace('PIECEA',     substr($number, 4, 4), $string);
        $string = str_replace('PIECEB',     substr($number, 8, 4), $string);
        return $string;
    }
    private function mask13($number): string
    {
        $string = "+NCODE (DDD) PIECEA-PIECEB";
        $string = str_replace('NCODE',      substr($number, 0, 2), $string);
        $string = str_replace('DDD',        substr($number, 2, 2), $string);
        $string = str_replace('PIECEA',     substr($number, 4, 5), $string);
        $string = str_replace('PIECEB',     substr($number, 9, 4), $string);
        return $string;
    }

    public function getFormatted(): string
    {

        $phone = $this->_value;
        switch (strlen($this->_value)) {
            case 8:
                $phone = $this->mask8($phone);
                break;
            case 9:
                $phone = $this->mask9($phone);
                break;
            case 10:
                $phone = $this->mask10($phone);
                break;
            case 11:
                $phone = $this->mask11($phone);
                break;
            case 12:
                $phone = $this->mask12($phone);
                break;
            case 13:
                $phone = $this->mask13($phone);
                break;
        }
        return $phone;
    }

    public function __toString()
    {
        return $this->_value;
    }
}

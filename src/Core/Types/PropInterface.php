<?php 

//Created: 2021-11-27
namespace RBFrameworks\Core\Types;

interface PropInterface {

    public function getPropName(): string;
    public function getPropValue();

    public static function buildFormatted(string $field, mixed $value = null):array;

    public static function buildValue(string $field, mixed $value = null);

}
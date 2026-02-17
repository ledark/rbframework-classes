<?php 

//Created: 2021-11-27

namespace RBFrameworks\Core\Types;

interface TypeInterface {

    //getFormatted usado para retornar o valor esperado após tratamento do Type
    public function getFormatted();

    //getNumber para extrair um valor numérico do Type
    public function getNumber():int;

    //getString para extrair um valor string do Type
    public function getString():string;

    //getValue para extrair um valor mixed
    public function getValue();
  
}
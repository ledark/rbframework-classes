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

    //verifica se o valor já está shrinked. do contrário, assume-se ser hydratado, retorna o valor shrinked, ou seja o shrink.
    public function getShrinked();

    //verifica se o valor já está hydratado. do contrário, assume-se ser shrinked, retorna o valor original, ou seja o hydrata.
    public function getHydrated();
  
}
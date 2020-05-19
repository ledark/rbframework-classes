<?php

namespace RBFrameworks\Interfaces;

interface Price {
    
    /**
     * Utiliza o ISO-4217 para currency, por exemplo:
     * BRL Real
     * USD Dolar Americano
     * EUR Euro
     * JPY Iene
     * BTC BitCoins
     * @return string 
     */
    public function getCurrency():string;
    
    /**
     * Quantidade de casas decimais dessa moeda
     * @return int
     */
    public function getDecimals():int;
    
    /**
     * Se vocк quer retonar R$ 1,00 ou R$ 1 й necessбrio forзar sempre um retorno em int para a maquina.
     * Isso traria 100 pensando em centavos. Se fosse uma libra esterlina (Reino Unido), retornaria 100 da mesma forma.
     * Moedas com mais casas decimais, como a Lнbia (LYD), deveriam retornar 1000 para simbolizar 1,000 Dinar
     * @samples BLR 1234,5 = BLR 123450
     * @return int
     */
    public function getCents():int;
    
    /**
     * Essa funзгo deveria ser capaz de retornar o valor monetбrio floated
     * @samples R$ 1234,50 returns 123450/100 [sendo 100 por getDecimals retornar 2]
     * @return int
     */
    public function getFloat():int;

    /**
     * 
     * @return string
     */
    public function getFormatedValue():string;
    
    public function setCents():int;
    
}

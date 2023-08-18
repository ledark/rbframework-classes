<?php 

namespace RBFrameworks\Core\Interfaces;

interface NotificationServiceInterface {

    /** Ação de Disparo do Serviço */
    public function send();
    
    /** Variável comum para Retornar Erros, se houverem */
    public function getErrors():array;

    /** Ação para trazer uma string de Template, com o body incluso dentro  */
    public function getTemplate():string;

}
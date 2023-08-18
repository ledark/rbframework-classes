<?php

namespace RBFrameworks\Database;

class Dao {
    
    public $Database;
    public $Model;
    public $Query;
    
    public function __construct(\RBFrameworks\Database $Database, Model $Model, Logger $Logger = null) {
        
        $this->Database = $Database;
        $this->Model = $Model;
        $this->Query = new Query();
        $this->Logger = (is_object($Logger)) ? $Logger : new Logger();
        $this->tabela = $Database->prefixo.str_replace($Database->prefixo, '', $Model->tabela);
        $this->prefixo = $Database->prefixo;
        
        //Configurar Query
        $this->Query
            ->setPrefix($Database->prefixo)
            ->useTables($Model->tabela)
        ;
        
        $this->registerQuery('all', "SELECT * FROM `$this->tabela`");
        
    }
    
    use DaoCommon;
    use DaoTable;
    use DaoQuery;

    public function build() {
        
        if(!$this->tableExists()) $this->createTable();

        //Varre todos os itens do model que possuem um prop[mysql]
        foreach($this->Model->model as $field => $props) {
            
            if(!$this->fieldExists($field)) $this->createField($field, $props);

        }
        
    }
    
}

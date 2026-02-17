<?php 

namespace RBFrameworks\Core\Database\Traits;

trait QueryUpsert {    
    /**
     * Essa ideia foi tirada de https://stackoverflow.com/questions/28295756/replace-into-without-checking-auto-increment-primary-key 
     * Porém, ainda não está implementada, e talvez o local da Query nem esteja no local certo.
     * @return string
     */
    public function render_upsert():string {
    /*    
        $tabela = reset($this->tables);
        
        return "INSERT INTO ".$tabela.' '.
    '('.parent::walk_query(['cod_produto'   => $cod_produto, 'qtd' => $estoque_in_erp['disponivel']], 'campos').')'.
    ' VALUES ('.$EstoqueEcom->walk_query(['cod_produto'   => $cod_produto, 'qtd' => $estoque_in_erp['disponivel']], 'values').')'.
    ' ON DUPLICATE KEY UPDATE '.
    parent::walk_query(['cod_produto'   => $cod_produto], 'update_values');
    */
        return '';
    }
}
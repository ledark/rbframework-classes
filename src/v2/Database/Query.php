<?php 

namespace RBFrameworks\Core\Database;

use RBFrameworks\Core\Config;

class Query {    
    
    use Traits\QueryVariables;
    use Traits\QueryCommon;
    use Traits\QuerySelect;
    use Traits\QueryUpdate;
    use Traits\QueryInsert;
    use Traits\QueryUpsert;
    use Traits\QueryDelete;
    use Traits\QueryWhere;
    use Traits\QueryLog;

    public function __construct() {
        $this->setPrefixo(Config::get('database.prefixo'));    
        $this->alfabeto = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        return $this;
    }

    /**
     * Renderiza o código, utilizando as configurações especificadas anteriormente.
     * @return string
     */
    public function render():string {
        if(!is_null($this->order)) $this->order = rtrim($this->order, ', ');
        $this->from = (is_null($this->from)) ? "`".reset($this->tables)."`" : $this->from;
        switch ($this->type) {
            case 'SELECT':
                return self::render_select();
            break;
            case 'UPDATE':
                return self::render_update();
            break;
            case 'INSERT':
                return self::render_insert();
            break;
            case 'UPSERT':
                return self::render_upsert();
            break;
            case 'DELETE':
                return self::render_delete();
            break;
            default:
                return self::render_select();
            break;
        }
    }

    public function renderRaw():string {
        $result = $this->render();
        $result = str_replace("\r\n", " ", $result);
        $result = str_replace("\t", " ", $result);
        $result = str_replace("  ", " ", $result);
        $result = str_replace("  ", " ", $result);
        $result = trim($result);
        return $result;
    }

}
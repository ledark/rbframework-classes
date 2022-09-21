<?php

namespace RBFrameworks\Core\Database;

use function PHPUnit\Framework\throwException;

class Modelv2Prop
{
    private $original_props;
    private $field;
    private $sql;
    private $props;

    /**
     * ['mysql' => 'SINTAXE', 'label' => 'NAME' => 'type'='text'] //Valid
     * [field => ['mysql' => 'SINTAXE', 'label' => 'NAME' => 'type'='text']] //Valid
     * [field => 'SINTAXE'] //Valid
     */
    public function __construct(array $props) {
        $this->original_props = $props;
        
        //SingleRow
        if(count($props) == 1) {
            $this->generateFieldAndSql($props);
            
        //MultiRows
        } else {
            foreach($props as $prop) {
                if(is_string($prop)) {
                    $this->sql = $prop;
                } else {
                    $this->generateFieldAndSql($prop);
                }
            }
        }

        if(!$this->isSql($this->sql)) {
            $this->field = 'NO_FIELD';
        }

    }

    private function generateFieldAndSql(array $props) {
        if (is_string(key($props)) and is_string($props[key($props)])) {
            $this->field = key($props);
            $this->sql = $props[key($props)];
            if($this->field == 'mysql' or $this->field == 'sql') {
                $this->field = 'NO_FIELD';
            }
            $this->props = [
                'sql' => $this->sql,
                'mysql' => $this->sql,
            ];
        } else 
        if (is_string(key($props)) and is_array($props[key($props)])) {
            $this->field = key($props);
            foreach($props[key($props)] as $prop => $value) {
                if(strtolower($prop) == 'sql' or strtolower($prop) == 'mysql') {
                    $this->sql = $value;
                    $this->props = [
                        'sql' => $this->sql,
                        'mysql' => $this->sql,
                    ];
                }
            }                
        }
    }

    public function getFieldName():string {
        return $this->field;
    }

    public function getSql():string {
        return $this->sql;
    }

    public function getProps():array {
        if(!isset($this->props)) $this->props = ['mysql' => 'VARCHAR(255) NOT NULL', 'sql' => 'VARCHAR(255) NOT NULL'];
        return $this->props;
    }

    public function getFieldAndProps():array {
        return [$this->getFieldName() => $this->getProps()];
    }

    private function isSql(string $sintaxe):bool {
        $sintaxe = strtolower($sintaxe);
        foreach(['char', 'int', 'decimal', 'enum', 'text'] as $regex) {
            if(strpos($sintaxe, $regex) !== false) return true;
        }
        return false;
    }

    private static function createFromMysqlDescribeRow(array $mysqlReturned):object {
        $convertNull = function(string $value):string {
            return strtolower($value) == 'NO' ? 'NOT NULL' : '';
        };

        $convertSpecial = function(string $Key, string $Extra):string {
            $r = ($Key == 'PRI') ? 'PRIMARY KEY ': ' ';
            $r.= ($Key == 'KEY') ? 'KEY ': ' ';
            $r.= ($Extra == 'auto_increment') ? 'AUTO_INCREMENT ': ' ';
            return $r;
        };

        extract($mysqlReturned);
        
        return new self([
            $Field => [
                'name' => $Field,
                'mysql' => "$Type ".$convertNull($Null)." ".$convertSpecial($Key, $Extra),
                'default' => isset($Default) ? $Default : '',
            ]
        ]);        
    }
    /**
     * Exemplo de mysqlReturned on database->query("DESCRIBE ?_minha_tabela")
     *      [Field] => cod
     *      [Type] => int(10) unsigned
     *      [Null] => NO
     *      [Key] => PRI
     *      [Default] =>
     *      [Extra] => auto_increment
     * @return void
     */    
    public static function createFromMysqlDescribe(array $mysqlReturned):array {
        $allProps = [];
        foreach($mysqlReturned as $i => $r) {
            $Prop = self::createFromMysqlDescribeRow($r);
            $allProps[$Prop->getFieldName()] = $Prop->getProps();
        }
        return $allProps;
    }

}

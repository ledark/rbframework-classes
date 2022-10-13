<?php

namespace RBFrameworks\Database;

use \RBFrameworks\Helpers\Collections;
use \RBFrameworks\Database;
use \RBFrameworks\Database\Model;
use \RBFrameworks\Database\Dao;
use \RBFrameworks\Database\SQLGetter as sql;

/*
O Objetivo dessa classe é ser uma Facade para várias funções úteis. Por isso ela com funções estáticas.
O ideal no processo de refatoração seria evitar o uso disso e aos poucos eliminar para que não haja mais static funcions.
*/
class Functions {
    
    private static $dao = null;

    public static function database(string $collection) {
        $modelInCollection = new \RBFrameworks\Helpers\Collections($collection);
        $Database = new \RBFrameworks\Database();
        $Model = new \RBFrameworks\Database\Model($modelInCollection->getCollection());
        return new \RBFrameworks\Database\Dao($Database, $Model);
    }

    public static function database_where(array $fields, string $sufix = 'OR'):string {
        $where = "AND (";
        foreach($fields as $field => $clause) {

            if(is_integer($clause)) {
                $clauseWhere = "= '$clause' {$sufix}";
            } else
            if(is_string($clause)) {
                $clauseWhere = "LIKE '%$clause%' {$sufix}";
            }


            $where.= "`{$field}` {$clauseWhere}";
        }
        return rtrim($where, $sufix).")";
    }

    public static function database_where_or($fields) { return self::database_where($fields, 'OR'); }
    public static function database_where_and($fields) { return self::database_where($fields, 'AND'); }

    public static function databaseq(string $sqlquery, array $replaces = []):array {
        $sql = new sql($sqlquery, $replaces);    
        return $sql->fetchSQL();   
    }
    
    //Deprecate
    public static function databaseq_v0(string $sqlquery, array $replaces = []):array {
        $query = $sqlquery;
        global $Database;
        if(isset($Database) and is_object($Database) and $Database instanceof \RBFrameworks\Database) {

        } else {
            $Database = new \RBFrameworks\Database();
        }
        if(!self::database_isSQL($sqlquery)) {
            $dao = self::database_dummy('DAO');
            $query = $dao->getRegistredQuery($sqlquery);
        }
        if(count($replaces)) {
            $query = smart_replace($query, $replaces, true);

        }
        if(isset($replaces['__return'])) {
            switch($replaces['__return']) {
                case 'query':
                    return [$sqlquery => $query];
                break;
            }
        }
        if(is_object($dao)) {
            
            $dao->saveRegistredQuery($sqlquery, $query);
        }
        print_r($dao);
        print_r($sqlquery);
        print_r($query);
        return $Database->setQuery($query)->execute()->getArray();
    }

    public static function database_dummy(string $return = 'DAO' ) {
        $Database = new \RBFrameworks\Database();
        $Model = new \RBFrameworks\Database\Model(['dummy' => ['field_dummy' => '']]);
        $Dao = new \RBFrameworks\Database\Dao($Database, $Model);
        switch(strtoupper($return)) {
            case 'DAO':
                return $Dao;
            break;
            default:
                return [$Database, $Model, $Dado];
            break;
        }
    }

    //Deprecate
    public static function database_isSQL(string $query) {
        $query = strtoupper($query);
        if(
            strpos($query, 'SELECT') !== false ||
            strpos($query, 'INSERT') !== false ||
            strpos($query, 'UPDATE') !== false ||
            strpos($query, 'DELETE') !== false 
        ) {
            return true;
        }
        return false;

    }
}

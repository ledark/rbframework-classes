<?php

use RBFrameworks\Core\Cache;
use RBFrameworks\Core\Database;

function database():Database {
    if(isset($GLOBALS['rbfdb'])) {
        return $GLOBALS['rbfdb'];
    }
    $GLOBALS['rbfdb'] = Database::getInstance();
    return $GLOBALS['rbfdb'];
}

function database_development():void {
    $config = config('database');
    $config['database'] = 'aliancasys_soxsox_development';
    $GLOBALS['rbfdb'] = new Database('table', [], $config);
}

function database_meekro():MeekroDB {
    if(isset($GLOBALS['meekrodb'])) {
        return $GLOBALS['meekrodb'];
    }
    $config = config('database');
    $GLOBALS['meekrodb'] = new MeekroDB($config['server'], $config['login'], $config['senha'], $config['database']);
    return $GLOBALS['meekrodb'];
}

function query(string $query, array $params = [], ?bool $encoding = null) {
    return queryBase($query, $params, $encoding, 'query');
}

function queryBase(string $query, ?array $params = null, ?bool $encoding = null, string $type = "query"):mixed {
    $database = new Database();
    switch($type) {
        case 'query':
            $result = $database->query($query, $params);
        break;
        case 'queryFirstRow':
            $result = $database->queryFirstRow($query, $params);
        break;
        case 'queryFirstField':
            $result = $database->queryFirstField($query, $params);
        break;
    }



    if($encoding === true) {
        $result = encoding($result);
    } else
    if($encoding === false) {
        $result = encoding_reverse($result);
    }
    return $result;
}

function queryFirstRow(string $query, array $params = [], ?bool $encoding = null) {
    return queryBase($query, $params, $encoding, 'queryFirstRow');
}

function queryFirstField(string $query, array $params = [], ?bool $encoding = null) {
    return queryBase($query, $params, $encoding, 'queryFirstField');
}

function getFieldListFromTable(string $table):array {
    return cache_stored(function() use($table) {
        $table = str_replace('?_', config('database.prefixo'), $table);
        $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$table}'";
        $res = database()->query($query);
        if(is_array($res)) {
            $fieldList = [];
            foreach($res as $r) {
                $fieldList[] = $r['COLUMN_NAME'];
            }
            return $fieldList;
        }
        return [];
    }, 'getFieldListFromTable3'.$table, 60*60*24*7);
}

/**
 * function getQueryUpdateListFromDados - Retorna uma string com os campos e valores para serem usados em um UPDATE
 * @param array $dados
 * @return string coluna1 = %s_coluna1, coluna2 = %s_coluna2, coluna3 = %i_coluna3, coluna4 = %i_coluna4
 */
function getQueryUpdateListFromDados(array $dados):string {
    $fieldList = $dados;
    $fieldList = array_map(function($field, $value) {
        $type = gettype($value);
        if($type == 'string') {
            return '`'.$field.'`'.' = %s_'.$field;
        }
        if($type == 'integer') {
            return '`'.$field.'`'.' = %i_'.$field;
        }
    }, array_keys($dados), array_values($dados));
    $res = implode(', ', $fieldList);
    return rtrim($res, ', ');
}
/**
 * function getQueryValuesFromDados - Retorna uma string com os campos e valores para serem usados em um INSERT
 * @param array $dados
 * @return string %s_coluna1, %s_coluna2, %i_coluna3, %i_coluna4
 */
function getQueryValuesFromDados(array $dados):string {
    $fieldList = $dados;
    $fieldList = array_map(function($field, $value) {
        $type = gettype($value);
        if($type == 'string') {
            return '%s_'.$field;
        }
        if($type == 'integer') {
            return '%i_'.$field;
        }
    }, array_keys($dados), array_values($dados));
    $res = implode(', ', $fieldList);
    return rtrim($res, ', ');
}

/**
 * function getQueryInsertListFromDados - Retorna uma string com os campos para serem usados em um INSERT
 * @param array $dados
 * @return string `coluna1`, `coluna2`, `coluna3`, `coluna4`
 */
function getQueryInsertListFromDados(array $dados):string {
    $fieldList = array_keys($dados);
    $fieldList = array_map(function($field) {
        return '`'.$field.'`';
    }, $fieldList);
    $res = implode(', ', $fieldList);
    return rtrim($res, ', ');
}

function upserting(string $table, array $dados, array $keys):array {
    try {
        database()->insert($table, array_merge($dados, $keys));
        return [
            'inserted' => true,
            'updated' => false,
            'cod_insert' => database()->insertId(),
            'affected_rows' => database()->affectedRows(),
        ];
    } catch(\Exception $e) {
        database()->update($table, $dados, $keys);
        return [
            'inserted' => false,
            'updated' => true,
            'cod_insert' => 0,
            'affected_rows' => database()->affectedRows(),
        ];
    }
}

function migration(array $assocFields, string $tablename) {

    $constructor = function($value):string {
        if(is_numeric($value)) {
            return 'INT NULL';
        }
        if(is_string($value)) {
            $value = strtolower($value);
            if(
                strpos($value, 'int(') !== false or
                strpos($value, 'varchar(') !== false or
                strpos($value, 'text ') !== false
            ) {
                return $value;
            }
        }
        return 'LONGTEXT NULL';
    };

    //Model as array ['field' => 'type', 'field2' => 'type2']
    $model = [];
    foreach($assocFields as $key => $value) {
        $model[$key] = $constructor($value);
    }

    $createTable = function($model) use ($tablename) {
        $fields = array_map(function($value, $key) {
            return "`$key` $value";
        }, $model, array_keys($model));

        $query = "CREATE TABLE IF NOT EXISTS {$tablename} (".implode(',', $fields).")";
        database()->query($query);
    };

    $updateTable = function($newColumns) use ($tablename) {
        $actualColumns = database()->queryFirstColumn("SHOW COLUMNS FROM {$tablename}");
        $previousColumn = $actualColumns[0];
        foreach($newColumns as $column => $type) {
            if(in_array($column, $actualColumns)) {
                $columnIndex = array_search($column, $actualColumns);
                $previousColumn = $actualColumns[$columnIndex];
            }
            if(!in_array($column, $actualColumns)) {
                database()->query("ALTER TABLE {$tablename} ADD COLUMN `{$column}` {$type} AFTER `{$previousColumn}`");
            }
        }
    };

    $createTable($model);
    $updateTable($model);

}
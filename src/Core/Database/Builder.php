<?php

namespace RBFrameworks\Core\Database;

use RBFrameworks\Core\Utils\Strings\Dispatcher;
use RBFrameworks\Core\Database\Modelv2;

class Builder
{

    public static function sqlFromProps(array $props):string {
        $Field = isset($props['Field']) ? $props['Field'] : uniqid('field_');
        $Type = isset($props['Type']) ? $props['Type'] : ''; // int(10) unsigned
        $Null = isset($props['Null']) ? $props['Null'] : ''; // NO
        $Null = ($Null == 'NO') ? 'NOT NULL' : '';
        $Key = isset($props['Key']) ? $props['Key'] : ''; // PRI
        $Key = ($Key == 'PRI') ? 'PRIMARY' : ''; //
        $Default = isset($props['Default']) ? $props['Default'] : ''; //
        $Extra = isset($props['Extra']) ? $props['Extra'] : ''; // auto_increment
        return "$Type $Null $Key $Extra $Key $Default";
    }

    public static function sqlCreateFromTable(string $table):string {
        $genericDatabase = new \Core\Database($table);
        $tableArr = $genericDatabase->queryFirstRow("SHOW CREATE TABLE {$genericDatabase->getTabela()}");
        return $tableArr['Create Table'];
    }

    public static function modelFromTable(string $table):Modelv2 {
        $genericDatabase = new \Core\Database($table);
        $tableArr = $genericDatabase->query("DESCRIBE {$genericDatabase->getTabela()}");
        $FldProps = [];
        foreach($tableArr as $i => $r) {
            $FieldName = $r['Field'];
            $FldProps[$FieldName] = [
                'label' => Dispatcher::label($FieldName),
                'mysql' => self::sqlFromProps($r),
            ];
        }
        return new Modelv2($FldProps);
    }
}

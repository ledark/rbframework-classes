<?php

namespace RBFrameworks\Core\Database;

use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Utils\Replace;
use RBFrameworks\Core\Config;

class Sql
{
    /**
     * Qualquer arquivo .sql que esteja em: $filename, Sql/$filename ou Query/$filename
     * O conteúdo deste arquivo é retornado, aplicando $replaces
     * Essa classe não valida o Sql.
     *
     * @param string $filename
     * @param array $replaces
     * @return string
     */
    public static function getFromFile(string $filename, array $replaces = []):string {
        
        $FileFinder = new File($filename, $replaces);

        $addSearchFolder = function(string $name) use ($FileFinder) {
            $name = rtrim($name, '\\'); $name = ltrim($name, '\\');
            $name = rtrim($name, '/');  $name = ltrim($name, '/');
            $name = $name.'/';
            $FileFinder
            ->addSearchFolder( dirname(debug_backtrace()[0]['file']).'/'.$name )
            ->addSearchFolder( dirname(debug_backtrace()[1]['file']).'/'.$name )
            ->addSearchFolder( dirname(debug_backtrace()[0]['file']).'/../'.$name )
            ->addSearchFolder( dirname(debug_backtrace()[1]['file']).'/../'.$name );
        };

        $addSearchFolder('Query');
        $addSearchFolder('Sql');
      
        $FileFinder->addSearchExtension('.sql');

        if(!$FileFinder->hasFile()) {
            throw new \Exception("$filename does not exist");
        }
        $originalContent = $FileFinder->getFileContents();
        $replacedContent = new Replace($originalContent, $replaces);
        return $replacedContent;
    }

    private static function getFromCore(string $filename, array $replaces = []):string {
        return self::getFromFile($filename, array_merge([
            'databaseName' => Config::get('database.database'),
            'prefixo' => Config::get('database.prefixo'),
        ], $replaces));
    }

    private static function getFromInformationSchema(string $tablename, string $schema_field):string {
        $tablename = ltrim($tablename, Config::get('database.prefixo'));
        $tablename = Config::get('database.prefixo').$tablename;
        return self::getFromCore('information_schema_tables', [
            'tableName' => $tablename,
            'field' => $schema_field,
        ]);
    }
    
    public static function getLastUpdatedTime(string $tablename):string {
        return self::getFromInformationSchema($tablename, 'UPDATE_TIME');
    }

    public static function getCreatedTimeTime(string $tablename):string {
        return self::getFromInformationSchema($tablename, 'CREATE_TIME');
    }

    public static function getRowCount(string $tablename):string {
        return self::getFromInformationSchema($tablename, 'TABLE_ROWS');
    }

}
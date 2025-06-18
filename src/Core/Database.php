<?php
/*
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. Neither the name of copyright holders nor the names of its
   contributors may be used to endorse or promote products derived
   from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL COPYRIGHT HOLDERS OR CONTRIBUTORS
BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * @author   "Ricardo Bermejo" <ricardo@bermejo.com.br>
 * @copyright Copyright (c) 2021 Ricardo Bermejo
 * @package  Core\Database
 * @version  1.1.0 [Core v1.98.2] Ago/2021
 * @license  Revised BSD
 */

namespace RBFrameworks\Core;

use MeekroDB;
use RBFrameworks\Core\Interfaces\isCrudable;
use RBFrameworks\Core\Database\Traits\Crud as CrudTrait;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Cache;

#[AllowDynamicProperties]
class Database implements isCrudable
{

    public $DB;
    public $database = '';
    public $prefixo = '';
    public $tabela;
    public $model;

    public $throw_exception_on_nonsql_error;
    public $uncaught;

    private $host;
    private $user;
    private $pass;

    private static $instance = null;

    /**
     * Configs
     *  extractConfig()
     *  getNumDimensions([]) 
     */
    use Database\Traits\Configs;

    /**
     * Connection
     *  resolvePrefixo
     *  resolveTabela
     *  resolveModel
     *  getPrefixo
     *  getTabela
     *  getModel
     *  getModelObject
     *  createModel
     *  setPrefixo
     *  setTabela
     *  setModel
     */
    use Database\Traits\Connection;

    /**
     * Model
     *  modelCheckStructure
     *  modelCheckStructure_asSimple
     *  modelCheckStructure_asLazy
     *  hasModel
     *  generateMysqlAndUncaught
     */
    use Database\Traits\Model;

    /**
     * Crud, TableOperations and TableQueryOperations
     */
    use CrudTrait;
    use Database\Traits\TableOperations;
    use Database\Traits\TableQueryOperations;
    use Database\Traits\Hooks;

    public function __construct(string $tabela = 'untitled_tablename', array $model = [], $config = null, bool $prefer_singleton = false)
    {

        //Configs
        $config = $this->extractConfig($config);

        //ServerDatabaseConn
        if($prefer_singleton) {
            $this->DB = database_meekro();
        } else {
            $this->DB = new MeekroDB($config['server'], $config['login'], $config['senha'], $config['database']);
        }
        $this->resolveMeekroDB();
        $this->defaultHandlers(is_null(Config::get('database.logs')) ? '' : Config::get('database.logs'));
        $this->database = $config['database'];
        $this->host = $config['server'];
        $this->user = $config['login'];
        $this->pass = $config['senha'];        
        //Resolvers [prefixo, tabela, model]
        $this->resolvePrefixo($config['prefixo']);
        $this->resolveTabela($tabela);
        $this->resolveModel($model);
        //AddMeekroHooks
        foreach(['pre_parse', 'pre_run', 'post_run', 'run_success', 'run_failed'] as $hook_name) {
            if(method_exists($this, $hook_name)) {
                $this->DB->addHook($hook_name, $this->$hook_name());
            }
        }        
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getPDOInstance():\PDO {
        return new \PDO($this->getDataSourceName(), $this->user, $this->pass);
    }

    private function resolveMeekroDB()
    {
        try {            
            $this->DB->error_handler = false; // disable standard error handler
            $this->DB->nonsql_error_handler = false; // disable standard error handler
            $this->DB->throw_exception_on_error = true; // throw exception on mysql query errors
            $this->DB->throw_exception_on_nonsql_error = true; // throw exception on library errors (bad syntax, etc)        
        } catch (\Exception $e) {
            Debug::log($e->getMessage(), [], 'MeekroDB.Exception','MeekroDB');
        }    
     }

    /**
     * Example of usage:
     * $myDatabase->defaultHandlers('LogAll')
     */
    public function defaultHandlers(string $behavior = ''): object
    {
        try {

            if (in_array($behavior, ['logAll', 'logSuccess']) === true) {
                $this->DB->error_handler  = function ($params) {
                    \RBFrameworks\Core\Debug::log("SUCCESS " . $params['query'] . " run in " . $params['runtime'] . " (milliseconds)", [], 'MeekroDB.Success', 'MeekroDB');
                };
            }
            if (in_array($behavior, ['logAll', 'logError']) === true) {
                $this->DB->error_handler = function ($params, $prefix = "") {
                    \RBFrameworks\Core\Debug::log("ERROR " . $params['query'] . " run in " . $params['runtime'] . " (milliseconds)", [], 'MeekroDB.Errors', 'MeekroDB');
                };
            }
        } catch (\Exception $e) {
            \RBFrameworks\Core\Debug::log($e->getMessage(), [], 'MeekroDB.Exception','MeekroDB');
        }
        return $this;
    }

    public function farray(string $query):array {
        return $this->query($query);
    }

    public function upserting(string $table, array $dados, string $where) {
        try {
            $this->insert($table, $dados);
            return [
                'inserted' => true,
                'updated' => false,
                'cod_insert' => $this->insertId(),
                'affected_rows' => $this->affectedRows(),
            ];
        } catch(\Exception $e) {
            $this->update($table, $dados, $where);
            return [
                'inserted' => false,
                'updated' => true,
                'cod_insert' => 0,
                'affected_rows' => $this->affectedRows(),
            ];
        }
    }

    public function __call(string $name, array $arguments)
    {
        if (in_array($name, ['table_exists'])) return call_user_func_array(array($this, $name), $arguments);
        if (in_array($name, ['query', 'queryFirstField', 'queryFirstRow', 'queryFirstList', 'queryFirstColumn', 'queryFullColumns', 'queryWalk', 'parse'])) $this->improveArgs($arguments);
        if (in_array($name, ['insert', 'update', 'delete', 'upserting'])) $this->improveFirstArgs($arguments);
        return call_user_func_array(array($this->DB, $name), $arguments);
    }

    /**
     * private function improveArgs
     * Aplicará automaticamente um replace para ?_ com o prefixo, quando a call for por query, queryFirstField ou queryFirstRow
     * @param array $arguments
     * @return void
     */
    private function improveArgs(&$arguments): void
    {

        $parser = function(string $query, string $filter = ""):string {
            $re = '/#FILTER::START\s(\w++)([\s\w\d\W]+)#FILTER::END/iuU';
            preg_match_all($re, $query, $matches, PREG_SET_ORDER, 0);
            foreach($matches as $match) {
                $varname = $match[1];
                $varcontent = $match[2];
                if($varname != $filter) {
                    $query = str_replace($match[0], '', $query);
                } else {
                    $query = str_replace($match[0], $varcontent, $query);
                }
            }
            return $query;
        };

        foreach ($arguments as &$arg) {
            if (is_string($arg)) {
                $filter = '';
                if(strpos($arg, '|') !== false) {
                    $arg = explode('|', $arg);
                    $filter = $arg[1];
                    $arg = $arg[0];
                }
                if(Config::assigned('query.'.$arg, false) !== false) {
                    $arg = Config::get('query.'.$arg);
                }
                $arg = $parser($arg, $filter);
                $arg = preg_replace('/(\?_)/m', $this->getPrefixo(), $arg);
            }
        }
    }

    public function getFieldListFromTable(string $table):array {
        return Cache::stored(function() use($table) {
            $table = str_replace('?_', $this->getPrefixo(), $table);
            $database = new Database();
            $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = %s";
            $res = $database->query($query, $table);
            if(is_array($res)) {
                $fieldList = [];
                foreach($res as $r) {
                    $fieldList[] = $r['COLUMN_NAME'];
                }
                return $fieldList;
            }
            return [];
        }, 'getFieldListFromTable2'.$table, 60*60*24*7);
    }

    private function improveFirstArgs(&$arguments):void {
        $count = 0;
        foreach($arguments as &$arg) {

            if($count == 0 and is_string($arg)) {
                if(strpos($arg, '?_') !== false) {
                    $table_name = $arg;
                }
                $arg = preg_replace('/(\?_)/m', $this->getPrefixo(), $arg);
            }

            //Prevent array $dados with invalid fields
            if(isset($table_name) and !empty($table_name) and is_array($arg)) {
                try {
                    $fieldList = $this->getFieldListFromTable($table_name);
                } catch(\Exception $e) {
                    $fieldList = [];
                }
                if(count($fieldList)) {
                    $res = array_intersect_key($arg, array_flip($fieldList));
                    if(count($res)) {
                        $arg = $res;
                    }
                }
            }

            $count++;
        }
    }


}

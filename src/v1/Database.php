<?php 

namespace Framework;

use Framework\Database\Traits\ConfigTrait;
use Framework\Database\Traits\MeekroTrait;
use MeekroDB;

class Database {

    private MeekroDB $meekrodb;
    private $database;
    private $host;
    private $user;
    private $pass;
    private $prefixo;

    use ConfigTrait;
    use MeekroTrait;

    public function __construct(mixed $config = null) {
        $config = $this->extractConfig($config);
        $port = $config['port']??'3306';
        $dsn = $config['type']??'mysql'.":host={$config['server']};port={$port};dbname={$config['database']};";
        $this->meekrodb = new MeekroDB($dsn, $config['login'], $config['senha']);
        $this->resolveMeekroDB();
        $this->defaultHandlers(is_null(Config::get('database.logs')) ? '' : Config::get('database.logs'));
        $this->database = $config['database'];
        $this->host = $config['server'];
        $this->user = $config['login'];
        $this->pass = $config['senha'];
        $this->prefixo = $config['prefixo'];
        foreach(['preparse', 'pre_run', 'post_run', 'run_success', 'run_failed'] as $hook_name) {
            if(method_exists($this, $hook_name)) {
                $this->meekrodb->addHook($hook_name, $this->$hook_name());
            }
        }
    }

    public static function getInstance():Database {
        return new self();
    }
    public static function getMeekroInstance():MeekroDB {
        return (new self())->meekrodb;
    }

    public function getPrefixo():string {
        return $this->prefixo;
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
        return call_user_func_array(array($this->meekrodb, $name), $arguments);
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

    private static function getValueType($value, string $key = ''):string {
        return match(gettype($value)) {
            'integer'   => '%i'.empty($key) ? '' : "_{$key}",
            'string'    => '%s'.empty($key) ? '' : "_{$key}",
            'array'     => '%l'.empty($key) ? '' : "_{$key}",
            default     => '%s'.empty($key) ? '' : "_{$key}",
        };
    }

    private static function getNamedArgs(array $assocData):array {
        $named = [];
        foreach($assocData as $key=>$value) {
            $named[$key] = self::getValueType($value, $key);
        }
        return $named;
    }




}
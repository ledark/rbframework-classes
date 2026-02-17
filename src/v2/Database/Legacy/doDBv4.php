<?php

namespace RBFrameworks\Core\Database\Legacy;

/*
 * 26/09/2016 - Método set() interpreta como WHERE se o $param2 for uma string
 * 25/05/2018 - Ajustes gerais para adatação ao RBFramewors v9.2
 * 21/11/2019 - Inclusão das funções replace, exists($key, $value) e cod_exists($key, $value)
 * 14/10/2021 - Update PDO and Namespaces
*/

use PDO;
use PDOException;
use Exception;
use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Plugin;

class doDBv4 extends PDO {

	public $server;
	public $login;
	public $senha;
	public $database;
	public $prefixo;

	public $PDOConstruct   = true;
	public $PDOErrors      = true;
	public $ADDErrors      = true;

	private $Meekro;
	public $preferMeekro = true;

	//Conexão e geração das Variáveis
	public function __construct(array $arrayModel, array $strconn = null) {
        		
		//Constructor
		if(is_null($strconn)) {
			Plugin::load("helper");
			$strconn = Config::get('database');
		}
		
        $server         = $strconn['server'];
        $login          = $strconn['login'];
        $senha          = $strconn['senha'];
        $database       = $strconn['database'];
        $prefixo        = $strconn['prefixo'];

		//Recuperar Variáveis
        $this->database = $database;
		$this->tabela = $prefixo.key($arrayModel);
		$this->model = reset($arrayModel);

		//Parallel Meekro
		$this->Meekro = new \RBFrameworks\Core\Database($this->tabela, $this->model, $strconn);

		//Constructor
		parent::__construct("mysql:host=$server;dbname=$database", "$login", "$senha");
		
		//Finalização
		if($this->PDOConstruct) {
			$this->check_table();
			$this->walk_model();
		}
	}
    
    /**
     * A ideia dessa função é inserir ou atualizar os dados, verificando a existência prévia deles no banco de dados
     * Para isso, você precisa passar dois parâmetros ARRAY, sendo:
     * O primeiro é o array dos dados que você deseja inserir/atualizar e o segundo parâmetro contém as chaves
     * Por exemplo: $this->replace($_POST, ['nome' => 'Ricardo']); //Irá cadastrar caso não exista um nome igual a Ricardo
     * 
     * @param array $dados arquivos para atualizar
     * @param array $chaves comparação das chaves exclusivas
     * @param string $return [cod para retornar o código da primaryKey, updated para true somente se atualizado e created para true somente se inserido
     */
    public function replace($dados = null, $chaves = null, $return = 'cod') {
        $toReturn = array();
        if(is_null($dados)) $dados = $_POST;
        if(is_null($chaves)) $chaves = $dados;
        $mountQuery = "";
        foreach($chaves as $field => $value) {
            $mountQuery.= "`$field` = '$value' AND ";
        }
        $mountQuery = rtrim($mountQuery, " AND ");
        $exists = $this->push("SELECT COUNT(`{$this->primary}`) FROM `{$this->tabela}` WHERE $mountQuery LIMIT 1");
        if($exists > 0) {
            $cod_insert = $this->push("SELECT `{$this->primary}` FROM `{$this->tabela}` WHERE $mountQuery LIMIT 1");
            $this->set($dados, $cod_insert);
            $toReturn = array(
                'cod' => $cod_insert,
                'updated' => true,
                'created' => false,
            );
        } else {
            $toReturn = array(
                'cod' => $this->add($dados),
                'updated' => false,
                'created' => true,
            );
        }
        return $toReturn[$return];
        
    }
    public function cod_exists($key, $value) {
        return $this->push("SELECT `{$this->primary}` FROM `{$this->tabela}` WHERE `$key` = '$value' LIMIT 1");
    }
    public function exists($key, $value) {
        $num = $this->push("SELECT COUNT(`{$this->primary}`) FROM `{$this->tabela}` WHERE `$key` = '$value' LIMIT 1");
        return ($num > 0) ? true : false;
    }    
	
	//Inserir Dados
	public function add($param1 = null, $return = false) {
		if(is_null($param1)) $param1 = $_POST;
		$campos = $this->walk_query($param1, 'campos');
		$values = $this->walk_query($param1, '?');
		$array = $this->walk_query($param1, 'array_values');
		$query = "INSERT INTO `{$this->tabela}` ($campos) VALUES ($values)";
		return $this->exe($query, $array, 'INSERT');
	}
	
	//Atualizar Dados [26/09/2016]
	public function set($param1, $param2, $return = false) {
		$array = $this->walk_query($param1, 'array_values');
		$update_query = $this->walk_query($param1, 'update');
		if( is_numeric($param2) ) {
			$query = "UPDATE `{$this->tabela}` SET $update_query WHERE `{$this->primary}` = '$param2' LIMIT 1";
		} else {
			$query = "UPDATE `{$this->tabela}` SET $update_query $param2";
		}
        
		return $this->exe($query, $array, 'UPDATE');	
	}
    
    public function set_skipWalker(array $setValues, array $whereChaves) {
        if(!isset($setValues['up'])) $setValues['up'] = time();
        $array = array();
        $query = "UPDATE `$this->tabela` SET ";
        foreach($setValues as $campo => $valor) {
            $query.= "`$campo` = ?, ";;
            $array[] = $valor;
        }
        $query = rtrim($query, ', ');
        $query.= " WHERE ";
        foreach($whereChaves as $campo => $valor) {
            $query.= "`$campo` = '$valor' ";
        }
        $query.= " LIMIT 1";
        return $this->exe($query, $array, 'UPDATE');	
    }
    
    
	
	//Deletar Dados
	public function del($param1) {
		//$p = $this->prepare("DELETE FROM `{$this->tabela}` WHERE `{$this->primary}` = $param1");
		//return $p;
		return $this->exe("DELETE FROM `{$this->tabela}` WHERE `{$this->primary}` = $param1");	
	}
	
	//Selecionar Dados
	public function get($param1 = null) {
		if(is_string($param1)) { 
			if(strpos(strtoupper($param1), 'WHERE ') === false) {
				$where = "WHERE $param1";
			}
		}
		if(is_numeric($param1)) $where = " WHERE `$this->primary` = $param1 ";
		$p = $this->farray("SELECT * FROM `$this->tabela` $where");
		return $p;
	}
	
	//Tratamento de Dados
	private function walk_query($param1, $return = '?') {
        
        
        
        
        $arr = array();
		$str = '';
		if(is_array($param1)) {
			foreach($param1 as $campo => $valor){
                if(!is_array($this->model) or !count($this->model)) {
                    if( !array_key_exists($campo, $this->model) ) {
                        if($this->ADDErrors) file_put_contents("log/logs/doDBv4.ADDERRORS", "[$this->tabela][$campo] Não Existe\r\n", FILE_APPEND );
                        continue;
                    }
                }
				switch($return) {
					case 'campos': 
						$str.= "`$campo`, ";
					break;
					case '?': 
						$str.= "?, ";
					break;
					case 'values': 
						$str.= "'$valor', ";
					break;
					case 'array_values': 
						$arr[] = $valor;
					break;
					case 'update':
						$str.= "`$campo` = ?, ";
            
					break;
				}
			}
            
			switch($return) {
				case 'array_values':
					return $arr;
				break;
				default:
					$str = rtrim($str, ", ");
					return $str;
				break;
			}
		}
	}
	
	private function walk_model() {
		foreach($this->model as $campo => $construct) {
			if(strstr($construct, 'PRIMARY KEY')) {
				$this->primary = $campo;
			}
			$this->field_exists($campo);
		}
	}
	
	private function exe($query, $array = array(), $type = null) {
		try {
			$this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$p = $this->prepare($query);
			if(count($array)) foreach($array as $i => $valor)	$p->bindValue($i+1, $valor);
			$p->execute();	
			if(is_null($type)) $type = strtoupper(substr($query, 0, 6));
			switch($type) {
				case 'INSERT':
					$p->closeCursor();
					return $this->push("SELECT LAST_INSERT_ID()");
				break;
				case 'NUM':
					$r = $p->fetchAll(PDO::FETCH_NUM);
					$p->closeCursor();
				break;
				case 'UPDATE':
					return $p->rowCount();
				break;
				case 'DELETE':
					return $p->rowCount();
				break;
				default:
					$r = $p->fetchAll(PDO::FETCH_ASSOC);
					$p->closeCursor();
				break;
			}
			return $r;
		} catch(PDOException $e) {
            
            


            $error_message = $e->getMessage();
            if(strpos($error_message, "1062 Duplicate entry") !== false) {
                print_r($query);
                echo "<hr/>duplicate entry";
                print_r($array);
                echo "<hr/>";
                print_r($type);
                die();
            }
            unset($error_message);
            
			if($this->PDOErrors) {
				$debugfile = debug_backtrace();
				$debugfilenum = count($debugfile);
				$debugfilecount = 0;
				$debug = '';
				foreach($debugfile as $debugs) {
					$debugfilecount++;
					if($debugfilecount == 1) continue;
                    if(!isset($debugs['file'])) $debugs['file'] = "";
                    if(!isset($debugs['line'])) $debugs['line'] = "";
					if(strpos ( $debugs['file'], '_app/render.php')) continue;
					if(strpos ( $debugs['file'], '/_filter.php') and strpos( $debugs['args'][0], 'render.php' )) continue;
					if(strpos ( $debugs['file'], '/index.php') and strpos( $debugs['args'][0], '_filter.php' )) continue;
					$debug.= $debugs['file'].':'.$debugs['line'].' ';
					
					if(is_array($debugs['args'])) {
						$args = '';
						foreach($debugs['args'] as $arg) {
							if(is_array($arg)) continue;
                            if(is_callable($arg)) continue;
                            $arg = (string)$arg;
							if($arg == $query) $arg = "QUERY";
							$args.= '"'.$arg.'", ';
						}
						$args = rtrim($args, ', ');
					}
					$debug.= '<span style="color: #999">';
					if(!empty($debugs['class'])) $debug.= '$'.$debugs['class'].$debugs['type'].$debugs['function'].'('.$args.');'; 
					else	$debug.= 'include('.$args.');';
					$debug.= '</span><br/>';
					
				}
				
			

				
				
				
				$debugfile = $debug;
				unset($debugs, $debug, $debugfilecount, $debugfilenum);
				if($e->getMessage() != 'SQLSTATE[HY000]: General error' ) {
					ob_start();
                    @print_r($array);
                    $array_verbose = ob_get_clean();
					Debug::log($query."\r\n".$array_verbose, [], 'error');
					Debug::log($e->getMessage(), [], 'error');
					echo "<div class=\"alert alert-danger\"><h3>PDOErrors</h3>". $e->getMessage() ." <hr/> <code>$query</code> <br/> $debugfile </div>";
				}
			}
		}	
	}
	
	//Retornar um array de qualquer query
	function farray($query, $paginar = 0) {
		return $this->exe($query);
	}
	
	//Retornar um array de qualquer query usando NUM
	function farrayn($query, $paginar = 0) {
		return $this->exe($query, array(), 'NUM');
	}	
	
	//Retornar um único valor usando um campo e o ID da chave primária
	public function getValue($campo, $cod) {
		$a = $this->farray("SELECT `$campo` FROM `$this->tabela` WHERE `$this->primary` = $cod LIMIT 1");
		return $a[0][$campo];
	}
	
	//Retornar um único valor de uma query
	public function push($query) {
		$a = $this->exe($query, array(), 'NUM');
		return $a[0][0];		
	}
    
    //Retorna o próximo valor do AUTO_INCREMENT
    public function getNextAutoIncrement() {
        
        global $database;
        if(!isset($database) or empty($database)) {
            global $RBVars;
            $database = $RBVars['database']['database'];
        }
        
        $tabela = $this->tabela;
        $q = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$database' AND TABLE_NAME = '$tabela'";
        return $this->push($q);
    }
	
	//Retornar uma tabela padrão de qualquer array
	function ftable($array = null, $error = '<div class="alert alert-danger">Nenhum resultado encontrado.</div>', $table_attr = 'class="table"') {
		if( is_null($array) ) {
			$array = $this->farray("SELECT * FROM `$this->tabela` LIMIT 1000");
		}
		if(!is_array($array) and is_string($array)) {
			$array = $this->farray($array);
		}
		if(!count($array)) {
			echo $error;
		} else {
			$num = 0;
			foreach($array as $i => $dados) {
				ob_start();
				$num++;
				if($num == 1) {
					echo '
					<table '.$table_attr.'>
					<thead>
						<tr>';
					foreach($dados as $chave => $valor) {
						//if(is_numeric($chave)) continue;
						echo '<th>'.$chave.'</th>';
					}
					echo '
						</tr>
					</thead>
					<tbody>
					';
				}
				echo '
					<tr>';
				foreach($dados as $chave => $valor) {
					//if(is_numeric($chave)) continue;
					echo '<td>'.$valor.'</td>';
				}
				echo '
					</tr>';				
				echo smart_replace(null, $dados);
			}
			echo '</tbody></table>';
		}
	}	

	public function table_exists(string $tablename = null):bool {
		if(is_null($tablename)) $tablename = $this->tabela;
		$query = "SHOW TABLES LIKE '{$tablename}'";
		$r = $this->exe($query);
		return (count($r)) ? true : false;
	}
	
	
	//Analisar se a tabela existe, para então criá-la
	private function check_table() {
		$query = "SHOW TABLES LIKE '{$this->tabela}'";
		$r = $this->exe($query);

		//Tabela Existe
		if(count($r)) {
			$last = false;
			foreach($this->model as $campo => $type) {
                $type = self::resetscope($type);
                if(substr($type, 0, 4) == 'null') continue;
				if(!$this->field_exists($campo)) {
					if($last)
					$q = 'ALTER TABLE `'.$this->tabela.'` ADD `'.$campo.'` '.$type.' AFTER `'.$last.'` ;'; else
					$q = 'ALTER TABLE `'.$this->tabela.'` ADD `'.$campo.'` '.$type.' FIRST;';
					$this->exe($q);
				}
				$last = $campo;
			}
			
			
		//Tabela Não Existe
		} else {
			global $prefixo;
			$this->create_table(array(substr($this->tabela, strlen($prefixo)) => $this->model));
		}
		unset($query, $p, $r);
	}
	
	//Criar Tabela
	private function create_table($array, $index = null, $unique = null, $key = null, $tipo = null) {
	
		global $prefixo;
		if($tipo == 'temp') $this->exe('DROP TABLE '.$prefixo.key($array).'');
		
		global $prefixo;
		foreach($array as $table => $params) {
            $inn = "";
			$pre = 'CREATE TABLE IF NOT EXISTS `'.$prefixo.$table.'` (';
			if($tipo == 'temp') $pre = 'CREATE TEMPORARY TABLE `'.$prefixo.$table.'` (';
			foreach($params as $campo => $sintaxe) {
				$sintaxe = trim($sintaxe);
				$sintaxe = rtrim($sintaxe, ',');
                if(substr($sintaxe, 0, 4) == 'null') continue;
				if(strpos($sintaxe, ' INDEX') !== false) { $index = $campo; $sintaxe = rtrim($sintaxe, ' INDEX');}
				if(strpos($sintaxe, ' ADDKEY') !== false) { $key = $campo; $sintaxe = rtrim($sintaxe, ' ADDKEY');}
				$inn.= '`'.$campo.'` '.$sintaxe.' ,';
			}
			$sux = ' ) ENGINE = MYISAM ';
			if($tipo == 'temp') $sux = ' ) ENGINE = MEMORY ';
		}
		
		if(is_null($index) and is_null($unique) and is_null($key)) $inn = rtrim($inn, ',');
		
		if(!is_null($index)) {
			$index = trim($index);
			$index = (substr($index, 0, 1) != '`') ? '`'.$index.'`' : $index;
			$index = 'INDEX ( '.$index.' ) ,';
		}
		if(!is_null($key)) {
			$key = trim($key);
			$key = (substr($key, 0, 1) != '`') ? '`'.$key.'`' : $key;
			$key = 'KEY ( '.$key.' ) ';
		} else {
			$index = rtrim($index, ',');
		}	
		$query = $pre.$inn.$index.$key.$sux;
		
		$this->exe($query);
	}

	//Query: Executor
	private function field_exists($campo) {
		if($this->preferMeekro) {
			return $this->Meekro->field_exists($campo);
		}
		$query = "SHOW COLUMNS FROM `{$this->tabela}` WHERE Field = '$campo'";
		$r = $this->exe($query);
		if(!count($r)) {
			return false;
		} else {
			return true;
		}
	}	
	
	//Utils: Function Scope
	public static function scope($string, $scope = '|') {
		if( strpos($string, $scope) !== false ) {
			$array = explode($scope, $string);
		} else {
			$array = [$string];
		}
		return $array;
	}
	
	//Utils: Function Scope
	public static function resetscope($string, $scope = '|') {
		if( strpos($string, $scope) !== false ) {
			$array = self::scope($string, $scope);
			return $array[0];
		} else {
			return $string;
		}
	}

	//Utils: Function Scope
	public static function secondscope($string, $scope = '|') {
		if( strpos($string, $scope) !== false ) {
			$array = self::scope($string, $scope);
			array_shift($array);
			return implode($scope, $array);
		} else {
			return $string;
		}
	}
	
}

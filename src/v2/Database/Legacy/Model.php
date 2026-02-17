<?php 

namespace RBFrameworks\Core\Database\Legacy;

use RBFrameworks\Core\Plugin;

class Model {
	
	var $iterator;
    
    public $type; //old ou new
	
	//Constructor
	public function __construct($array = null, $values = null) {
		if(is_null($array)) $array = array();
		$this->model = $array;
		if(is_array($values)) {
			$this->check();
			foreach($values as $campo => $valor) {
				if(isset($this->model[$campo])) $this->model[$campo]["value"] = $valor;
			}
		}
	}
	/*
	$Model
		->get('nome.alias')
		->get('nome.atributo')
	*/	
	public function get($param1 = null, $param2 = null) {
		if( is_null($param1) and is_null($param2) ) return $this->model;
		if( strpos($param1, '.') !== false ) {
			$campo = strstr($param1, '.', true);
			$property = str_replace('.', '', strstr($param1, '.'));
			return $this->model[$campo][$property];
		} else {
			$campo = $param1;
			if( is_null($param2) ) {
				return $this->model[$campo];
			} else 
			if( is_string($param2) ) {
				$property = $param2;
				return $this->model[$campo][$property];
			}
		}
	}
	/*
	$Model->getOld();  //Serve para retornar um array contendo o model antigo, onde os campos continham apenas uma string com o mysql code.
	*/
	public function getOld() {
		$old = array();
		$this->check();
		foreach($this->model as $campo => $data) {
			$old[$campo] = $data['mysql'];
		}
		return $old;
	}
	/*
	$Model
		->set('campo1'			, 'alias'			, 'Valor do Alias')				//Cria ou modifica no model o campo1, com a propriedade alias e seu Valor do Alias
		->set('campo1.alias'	, 'Valor do Alias')									//Idem
		->set('campo1'			, array('alias' => 'Valor do Alias'))				//Idem
		->set('campo1'			, array('alias' => 'Valor do Alias'),	true)		//Sobrescreve todo o model pelo array informado.
		->set('campo1',	'keyword')													//Pr�defini��es de cria��o do campo1
	*/
	private function parse_simple($param1 = null, $param2 = null, $param3 = null) {
		
		if($param1 == null) {
			$param1 = 'field_'.count($this->model);
		}
		
		//$this->parse("campo.property");
		if( strpos($param1, '.') !== false ) {
			$campo = trim(strstr($param1, '.', true));
			$property = trim(str_replace('.', '', strstr($param1, '.')));
			$value = $param2;
		} else {
			$campo = $param1;
			//$this->parse("campo", array());
			if( is_array($param2) and is_null($param3) ) {
				$property = 'ARRAY';
				$value = (isset($this->model[$campo])) ? serialize(array_merge($this->model[$campo], $param2)) : serialize($param2);
				$value = base64_encode($value);
			} else
			//$this->parse("campo", array(), true);
			if( is_array($param2) and $param3 === true ) {
				$property = $param2;
				$value = 'NO_VALUE';
			} else 
			//$this->parse("campo", "property", "value");
			if( is_string($param2) and !is_null($param3) ) {
				$property = $param2;
				$value = $param3;
			} else 
			if( is_string($param2) and is_null($param3) ) {
				//$this->parse("campo", "property");
				$property = $param2;
				$value = null;
			}
		}			
		return array($campo, $property, $value);
	}	
	
	public function set($param1 = null, $param2 = null, $param3 = null) {
		if( strpos($param1, '.') !== false ) {
			$campo = trim(strstr($param1, '.', true));
			$property = trim(str_replace('.', '', strstr($param1, '.')));
			if(!empty($param2)) $value = $param2;
			$this->model[$campo][$property] = $value;
			
		} else {
			$campo = $param1;
			if( is_array($param2) and is_null($param3) ) {
				$this->model[$campo] = array_merge($this->model[$campo], $param2);
			} else
			if( is_array($param2) and $param3 === true ) {
				$this->model[$campo] = $param2;
			} else 
			if( is_string($param2) and !is_null($param3) ) {
				$this->model[$campo][$param2] = $param3;
			} else 
			if( is_string($param2) and is_null($param3) ) {
				switch($param2) {
					case 'primary':
						$this->model[$campo]['alias'] = 'C�d. Database';
						$this->model[$campo]['mysql'] = 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY';
						$this->primary = $campo;
					break;
					case 'textarea':
						$this->model[$campo]['mysql'] = 'LONGTEXT NOT NULL';
					break;
					case 'int':
						$this->model[$campo]['mysql'] = 'INT(10) UNSIGNED NOT NULL';
					break;
					default:
						$this->model[$campo][$param2] = '';
					break;
				}
			}
		}
		$this->iterator = $campo;
		return $this;
	}
	/*
	$Model
		->check()	//Essa fun��o tentar� garantir um Model consistente, adicionando campos importantes, como o mysql e name por exemplo
	*/
	public function check() {
		$campos = array();
		$newModel = array();
		foreach($this->model as $campo => $data) {
			$campos[] = $campo;
			if(is_string($data)) {
				$newModel[$campo]['mysql'] = $data;
			} else 
			if(is_array($data)) {
				foreach($data as $property => $valor) {
					$newModel[$campo][$property] = $valor;
				}
			}
		}
		//Completando Valores Inexistentes na $Model
		foreach($campos as $campo) {
			if( !isset($newModel[$campo]['mysql']) )	$newModel[$campo]['mysql'] = 'VARCHAR(255) NOT NULL';
			if( !isset($newModel[$campo]['alias']) )	$newModel[$campo]['alias'] = ucwords( str_replace('_', ' ', $campo) ) ;
			if( !isset($newModel[$campo]['name']) )		$newModel[$campo]['name'] = $campo;
			if( !isset($newModel[$campo]['value']) )	$newModel[$campo]['value'] = "";
		}
		$this->model = $newModel;
		return $this;
	}
	
	public function add($campo, $value, $adicional = null) {
		
		//Dividir o Model em duas partes, com base na posi��o do $this->iterator
		global $slicedA, $slicedB;
		$slicedA = array();
		$slicedB = array();
		
		array_walk($this->model, function($property, $campo){
			global $slicedA, $slicedB;
			if($campo == $this->iterator) {
				$slicedB[$campo] = $property;
			}
			if(count($slicedB)) {
				$slicedB[$campo] = $property;
			} else {
				$slicedA[$campo] = $property;
			}
		});
		
		$slicedA[$this->iterator] = array_shift($slicedB);
		
		/*
			O resultado da divis�o ficar� em $slicedA e $slicedB
			Ent�o a fun��o $this->parse descobre o nome do campo e da propriedade para aplicar o valor
		*/
		
		list($campo, $property, $value) = $this->parse_simple($campo, $value, $adicional);
		
		if($property == 'ARRAY') {
			$slicedA[$campo] = unserialize(base64_decode($value));
		} else {
			$slicedA[$campo][$property] = $value;
		}
		
		
		$this->model = array_merge($slicedA, $slicedB);
		
		unset($slicedA, $slicedB);		
		return $this;
	}
	
	public function addHtml($htmlcode) {
		$prop = array(
			'type'	=>	'html'
		,	'value'	=> $htmlcode
		);
		$this->add('field_'.count($this->model), $prop);
		return $this;
	}
	
    /**
     * Transforma um array contendo um model estilo novo para um antigo e retorna o model antigo
     * @param array $new
     * @return array
     */
    public static function new2old($new) {
	   Plugin::load("utf8_encode_deep");
       $model = array();
       foreach($new as $field => $props) {
           utf8_encode_deep($props);
           if(!isset($props['mysql'])) continue;
           if(empty($props['mysql'])) continue;
           $model[$field] = $props['mysql']." /* ".json_encode($props)." */";
       }
       return $model;
    }
    
    /**
     * Adiciona um par�metro em um $model e devolve esse model com  o par�metro adicionado
     * @param type $model
     * @param type $param
     * @param type $value
     */
    public static function addParam(&$model, $param, $value) {
        
        Plugin::load("utf8_encode_deep");
        
        //Adicionar Parâmetro em um $model old
        if(!is_array($model)) {
            $arr = self::getParams($model);
            $arr[$param] = $value;
            array_walk_recursive($arr, function(&$valor, $chave){
                if(is_numeric($valor)) $valor = strval($valor);
            });
            $model = $arr['mysql']." /* ".json_encode_nice($arr)." */";            
        } else

        //Adicionar Parâmetro em um $model new
        if(is_array($model)) {
            $model[$param] = $value;
        }
        
    }
    
    public static function getParams($str) {
        $re = '~/\*(.*?)\*/~s';
        preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0);
        if(!count($matches)) {
            return array();
        } else {
            Plugin::load("json");
            return json_decode_nice(($matches[1][0]), true);
        }        
    }    

	
}
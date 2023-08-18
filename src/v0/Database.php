<?php

namespace RBFrameworks;


class Database extends \PDO {
    
    use Database\Generic;
    use Database\Result;
    use Database\PDO;
    use Database\Config;
        
    public $query = '';
    public $statement = null;
    
    /**
     *  Creates a PDO instance representing a connection to a database 
     */    
	public function __construct($databaseConfig = 'doDBv4') {
        
        $config = $this->getConfig($databaseConfig);
                
        $server = $config['server'];
        $database = $config['database'];
        $login = $config['login'];
        $senha = $config['senha'];

        parent::__construct("mysql:host=$server;dbname=$database", "$login", "$senha");
        
        $this->prefixo = $config['prefixo'];
        $this->database = $config['database'];
        
        /*
        //Constructor
        if(is_null($strconn)) global $server, $login, $senha, $database, $prefixo;
        
        global $RBVars;
		
        $server         = $RBVars['database']['server'];
        $login          = $RBVars['database']['login'];
        $senha          = $RBVars['database']['senha'];
        $database       = $RBVars['database']['database'];
        $prefixo        = $RBVars['database']['prefixo'];
        
        if(is_array($strconn)) list($server, $login, $senha, $database, $prefixo) = $strconn;
        
        global $PDOConstruct, $PDOErrors, $ADDErrors;
        
        $PDOConstruct   = $RBVars['database']['PDOConstruct'];
        $PDOErrors      = $RBVars['database']['PDOErrors'];
        $ADDErrors      = $RBVars['database']['ADDErrors'];

		//Recuperar Variáveis
        $this->database = $database;
		$this->tabela = $prefixo.key($arrayModel);
		$this->model = reset($arrayModel);

		//Constructor
		parent::__construct("mysql:host=$server;dbname=$database", "$login", "$senha");
		
		//Finalização
		if($GLOBALS['PDOConstruct']) {
			$this->check_table();
			$this->walk_model();
		}
        */
	}


}

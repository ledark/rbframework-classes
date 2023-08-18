<?php

namespace RBFrameworks\Database;

trait Config {    

    private function getConfig($databaseConfig):array {
        if(is_string($databaseConfig) and $databaseConfig == 'doDBv3') {
            
            $config = [
                'server' => $GLOBALS['server'],
                'login' => $GLOBALS['login'],
                'senha' => $GLOBALS['senha'],
                'database' => $GLOBALS['database'],
                'prefixo' => $GLOBALS['prefixo'],
            ];
            
        } else
        if(is_string($databaseConfig) and $databaseConfig == 'doDBv4') {

            global $RBVars;
            $config = [
                'server' => $RBVars['database']['server'],
                'login' => $RBVars['database']['login'],
                'senha' => $RBVars['database']['senha'],
                'database' => $RBVars['database']['database'],
                'prefixo' => $RBVars['database']['prefixo'],
            ];
            
        } else
        if(is_string($databaseConfig)) {
            $config = (new Config())->extract($databaseConfig);
        } else
        if(is_array($databaseConfig)) {
            $config = $databaseConfig;
        } else
        if(is_null($databaseConfig)) {
            throw new Exception("Configura��o de acess ao Banco de Dados n�o encontrada.");
        }
        return $config;
    }
    
}

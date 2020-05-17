<?php

namespace RBFrameworks\Router;

plugin("dispatcher");

trait RouterClass {
    
    private function createClassAttemptsWithParts($uri, $prefix, $tentativa):array {
        $parts = explode('/', $uri);
        switch($tentativa) {
            default:
                $config = [
                    'OriginalReq'   => '/'.$uri,
                    'ClassPrefix'   => $prefix,
                    'ClassName'     => $tentativa,
                    'MethodName'    => array_shift($parts),
                    'Params'        => $parts,
                ];                
            break;
            case 'LEVEL_1':
                $config = [
                    'OriginalReq'   => '/'.$uri,
                    'ClassPrefix'   => $prefix,
                    'ClassName'     => array_shift($parts),
                    'MethodName'    => array_shift($parts),
                    'Params'        => $parts,
                ];
                if(empty($config['MethodName'])) $config['MethodName'] = 'index';
            break;
            case 'LEVEL_2':
                $config = [
                    'OriginalReq'   => '/'.$uri,
                    'ClassPrefix'   => $prefix,
                    'ClassName'     => array_shift($parts).'\\'.array_shift($parts),
                    'MethodName'    => array_shift($parts),
                    'Params'        => $parts,
                ];
                if(empty($config['MethodName'])) $config['MethodName'] = 'index';
                if(is_numeric($config['MethodName'])) {
                    $config['Params'][] = $config['MethodName'];
                    $config['MethodName'] = 'index';
                }
            break;
        }
        return $config;
    }

    /**
     * Essa classe, executada em searchClass é responsável por usar a $uri da Request e gerar todas as tentativas possíveis.
     * Uma tentativa, é uma array AttemptClass
     * @param string $uri
     * @param string $prefix
     * @return array
     */
    public function createClassAttempts(string $uri, string $prefix = 'Controllers\\', string $ClassDefault):array {
        
        $attempts = [];

        if(empty($uri)) {
            return [0 => [
                   'ClassPrefix'   => $prefix,
                    'ClassName'     => $ClassDefault,
                    'MethodName'    => 'index',
                    'Params'        => [],                
            ]];
        }
        
        $parts = explode('/', $uri);
        $attempts[] = $this->createClassAttemptsWithParts($uri, $prefix, $ClassDefault);
        $attempts[] = $this->createClassAttemptsWithParts($uri, $prefix, 'LEVEL_1');
        $attempts[] = $this->createClassAttemptsWithParts($uri, $prefix, 'LEVEL_2');
        
        $res = [];
        foreach($attempts as $configs) {
            $res[] = $configs;
            if(isset($configs['ClassName'])) {
                $configs['ClassName'] = ucwords($configs['ClassName'], '\\');
            }
            if(isset($configs['MethodName'])) {
                $configs['MethodName'] = dispatcher_camelcased($configs['MethodName'], true);
            }
            $res[] = $configs;
        }
        
        return $res;
    }
    
    

}

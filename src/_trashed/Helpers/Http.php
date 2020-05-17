<?php

namespace RBFrameworks\Helpers;

class Http {
    
    private $url = "";
    protected $ch = null;
    
    //Basicos
    private $port = 80;
    private $method = ""; //GET or POST
    private $body = "";
    private $headers = array();
    
    //Responses
    private $result;
    private $response;
    private $errors;    

    //Abrir Conexão
    public function __construct(string $url) {
        $this->url = $url;
        $this->ch = curl_init($url);
    }
    
    public function render() {
        $response = $this->getResponse();
        return substr($this->getResult(), $response['header_size']);
    }
    
    //Setters Básicos
    public function setPort(int $port) {
        $this->port = $port;
        if($this->port != 80) curl_setopt($this->ch, CURLOPT_PORT, $this->port);
        return $this;
    }
    
    public function setMethod(string $method) {
        $this->method = $method;
        if($this->method == 'GET') {
            curl_setopt($this->ch, CURLOPT_HTTPGET, true);

        } else
        if($this->method == 'POST') {
            curl_setopt($this->ch, CURLOPT_POST, true);
            if(is_array($this->body)) {
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($this->body));
            } else {
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->body);
            }
        }        
        return $this;
    }
    
    public function setBody($body) {
        $this->body = $body;
        return $this;
    }
    
    public function setHeaders($header) {
        if(is_string($header)) {
            $this->headers[] = $header;
        } else
        if(is_array($header)) {
            $this->headers = array_merge($this->headers, $header);
        }
        if(count($this->headers)) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        }        
        return $this;
    }
    
    //Definir todas as Options usadas no cURL
    public function setOptions() {
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HEADER, true);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($this->ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, false);
        curl_setopt($this->ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($this->ch, CURLOPT_CERTINFO, true);
        curl_setopt($this->ch, CURLOPT_VERBOSE, true);
        curl_setopt($this->ch, CURLOPT_CRLF, true);
        curl_setopt($this->ch, CURLOPT_FAILONERROR, true);
        curl_setopt($this->ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($this->ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($this->ch, CURLOPT_FTP_USE_EPRT, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt($this->ch, CURLOPT_PROTOCOLS, CURLPROTO_ALL );
        return $this;
    }

    public function setOption($name, $value) {
        curl_setopt($this->ch, $name, $value);
        return $this;
    }    
    
    public function run() {
        $this->result = curl_exec($this->ch);
        $this->response = curl_getinfo($this->ch);
        $this->errors = curl_error($this->ch);
        return $this;
    }
    
    
    use Http\traitGetters;
    
    //Fechar Conexão
    public function __destruct() {
        curl_close($this->ch);
    }    
    
    /**
     * Função mantida para fins de retrocompatibilidade
     */
    public function exec():array {
        if(empty($this->method)) $this->setMethod("GET");

        $this
            ->setOptions()
            ->run()
        ;

        return array(
            'result'    =>  $this->getResult()
        ,   'response'  =>  $this->getResponse()
        ,   'errors'    =>  $this->getErrors()
        );
    }


    
}

/*
  

    
    //Alias para processar um GET
    public function get($info = null) {
        $this->setMethod("GET");
        $this->setOptions();
        $result = $this->exec();
        return is_null($info) ? $result : $result[$info];
    }    
    
    //Alias para processar um POST
    public function post($info = null) {
        $this->setMethod("POST");
        $this->setOptions();
        $result = $this->exec();
        return is_null($info) ? $result : $result[$info];
    }    
    

    

*/
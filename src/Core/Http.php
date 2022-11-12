<?php

namespace RBFrameworks\Core;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\SessionCookieJar as GuzzleSession;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use phpQuery;
use RBFrameworks\Core\Config;

/**
 * samples:
 * (new Http('http://sample.com'))
 */

class Http {

    public $client = null;
    public $options = [];
    public $method = 'GET';
    public $uri = null;
    public $requestData = null;
    private $jar = null;
    private $requestObj = null;
    private $responsetObj = null;
    public $expectedStatusCodes = [200]; //Range of StatusCode that no throw erros

    public function __construct(string $uri, array $requestData = []) {
        $this->setUri($uri);
        $this->setRequestData($requestData);
    }

    public function setExpectedStatusCodes(array $expectedStatusCodes):object {
        $this->expectedStatusCodes = $expectedStatusCodes;
        return $this;
    }

    public function addOption(string $key, $value):object {
        $this->options[$key] = $value;
        return $this;
    }
    public function setOptions(array $options):object {
        $this->options = $options;
        return $this;
    }
    public function addOptions(array $options):object {
        $this->options = array_merge($this->options, $options);
        return $this;
    }
    public function setUri(string $uri):object {
        $this->uri = $uri;
        return $this;
    }
    public function setMethod(string $method):object {
        $this->method = $method;
        return $this;
    }
    public function setRequestData(array $requestData):object {
        $this->requestData = $requestData;
        return $this;
    }

    private function getClient():object {
        if(is_null($this->client)) {
            $this->client = new GuzzleClient($this->getOptions());
        }
        return $this->client;
    }
    public function getOptions():array {
        return $this->options;
    }
    public static function getRequestUri():string {
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
          }
          else {
            if (isset($_SERVER['argv'])) {
              $uri = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['argv'][0];
            }
            elseif (isset($_SERVER['QUERY_STRING'])) {
              $uri = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
            }
            else {
              $uri = $_SERVER['SCRIPT_NAME'];
            }
          }
          // Prevent multiple slashes to avoid cross site requests via the Form API.
          $uri = '/' . ltrim($uri, '/');
        
          return $uri;
    }

    public function getUri():string {
        return $this->uri;
    }
    public function getMethod():string {
        return $this->method;
    }
    public function getRequestData():array {
        return $this->requestData;
    }

    public function doGet() {
        $this->setMethod('GET');
        return $this->doRequest($this->getMethod(), $this->getUri(), $this->getRequestData());
    }

    public function asGet():object { $this->setMethod('GET'); return $this; }
    public function asPost(array $data = null):object { 
        $this->setMethod('POST'); 
        if(is_array($data)) $this->setRequestData($data);
        return $this; 
    }
    public function asPut():object { $this->setMethod('PUT'); return $this; }
    public function asPatch():object { $this->setMethod('PATCH'); return $this; }
    public function asDelete():object { $this->setMethod('DELETE'); return $this; }
    public function asOptions():object { $this->setMethod('OPTIONS'); return $this; }
    public function asHead():object { $this->setMethod('HEAD'); return $this; }

    public function doPost() {
        $this->setMethod('POST');
        return $this->doRequest($this->getMethod(), $this->getUri(), $this->getRequestData());
    }

    public function doRequest(string $method = null, string $uri = null, array $post = null) {
        if(!is_null($this->responsetObj)) return $this->responsetObj;
        if(is_null($method)) $method = $this->getMethod();
        if(is_null($uri)) $uri = $this->getUri();
        $post = is_null($post) ? $this->getRequestData() : ['form_params' => $post];
        $client = $this->getClient();
        $this->responsetObj = $client->request($method, $uri, $post);
        return $this->responsetObj;
    }

    public function setJar(GuzzleSession $jar):object {
        $this->jar = $jar;
        return $this;
    }
    public function getJar():GuzzleSession {
        if(is_null($this->jar)) $this->jar = new GuzzleSession('PHPSESSID', true);
        return $this->jar;
    }
    public function useSession():object {
        $this->addOption('cookies', $this->getJar());
        return $this;
    }

    /*** Common Responses */

    public function getHttpResponse(callable $callback = null):string {
        $res = $this->doRequest();
        if(in_array($res->getStatusCode(), $this->expectedStatusCodes)) {
            if(is_null($callback)) return $res->getBody();
            $body = $res->getBody();
            $doc = phpQuery::newDocument($body);

            if(is_callable($callback)) {
                return $callback($body, $doc);
            }            
            
            return (string) $body;
        }
        return '';
    }

    public function getPhpQueryResponse():object {
        $res = $this->doRequest();
        if(in_array($res->getStatusCode(), $this->expectedStatusCodes)) {
            $body = $res->getBody();
            return phpQuery::newDocument($body);
        }
    }

    public function getTextResponse():string {
        $res = $this->doRequest();
        if(in_array($res->getStatusCode(), $this->expectedStatusCodes)) {
            $actual = $res->getBody();
            $actual = $actual->getContents();
            return (string) $actual;
        }
        return '';
    }

    public function getInnerHtmlFromPhpQueryNode($node) {
        $innerHTML= ''; 
        $children = $node->childNodes; 
        foreach ($children as $child) { 
            $innerHTML .= $child->ownerDocument->saveXML( $child ); 
        } 
        return $innerHTML;        
    }

    public function getHtmlHeadResponse(bool $innerHtml = false):string {
        return $this->getHttpResponse(function($body, $doc) use ($innerHtml) {
            if(isset($doc['head'])) {
                if($innerHtml) return $this->getInnerHtmlFromPhpQueryNode($doc['head']->get()[0]);
                return $doc['head']->get()[0]->nodeValue;
            } 
            return 'No Head Found';
        });
    }

    public function getHtmlBodyResponse(bool $innerHtml = false):string {
        return $this->getHttpResponse(function($body, $doc) use ($innerHtml) {
            if(isset($doc['body'])) {
                if($innerHtml) return $this->getInnerHtmlFromPhpQueryNode($doc['body']->get()[0]);
                return $doc['body']->get()[0]->nodeValue;
            } 
            return 'No Body Found';
        }); 
    }

    public function getHtmlPqResponse(string $tagName, callable $callback = null):string {
        return $this->getHttpResponse(function($body, $doc) use ($callback, $tagName) {
            $doc['body']->get()[0]->nodeValue;
            if(is_callable($callback)) {
                return $callback($body, $doc);
            }  
            return pq($tagName);
        });
    }
    
    public function getJsonResponse():array {
        $res = $this->doRequest();
        if(in_array($res->getStatusCode(), $this->expectedStatusCodes)) {
            $actual = $res->getBody();
            $actualContents = $actual->getContents();
            $actualJson = json_decode($actualContents, true);
            return is_null($actualJson) ? ['guzzle_error' => 'noResponseJson', 'actual_response' => $actualContents] : $actualJson;
        }
        return [];
    }
    public static function getHost():string {
        $_SERVER['REQUEST_SCHEME'] = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
        if(substr($_SERVER['REQUEST_SCHEME'], -3) != '://') $_SERVER['REQUEST_SCHEME'] = $_SERVER['REQUEST_SCHEME'] . '://';
        $_SERVER['REQUEST_SCHEME'] = str_replace(':', '', $_SERVER['REQUEST_SCHEME']);
        $_SERVER['REQUEST_SCHEME'] = str_replace('/', '', $_SERVER['REQUEST_SCHEME']);
        $_SERVER['REQUEST_SCHEME'] = $_SERVER['REQUEST_SCHEME']. '://';
        return $_SERVER['REQUEST_SCHEME'] . self::getDomain();
    }
    public static function getSite():string {
        $script = basename($_SERVER['SCRIPT_NAME']); //framework aka index.php
        return self::getHost(). substr($_SERVER['SCRIPT_NAME'], 0, strlen($script)*-1); //remove index.php 
    }

    public static function getDomain():string {
        return Config::get('server.http_host');
    }
    
    public static function redir(string $uri) {
        header('Location: ' . $uri);
        exit();
    }

    public static function isAbsolute(string $uri):bool {
        if(substr($uri, 0, 2) == '//') return true;
        if(substr($uri, 0, 7) == 'http://') return true;
        if(substr($uri, 0, 8) == 'https://') return true;
        return false;
    }

    public static function sanitizeUri(string $uri):string {

        if(!self::isAbsolute($uri)) {
            $baseUrl = Config::get('server.server.baseUrl');
            $mountedUrl = rtrim($baseUrl, '/').'/'.ltrim($uri, '/');
            $uri = $mountedUrl;
        }
       
        return $uri;

    }    

}
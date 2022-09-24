<?php

namespace Core\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\SessionCookieJar as GuzzleSession;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

class Provider implements HttpRequestInterface
{
    public $uri = '';
    public $client = null;
    public $method = 'GET';
    public $options = [
      'debug' => false,
      //'form_params' => $form_params,
      'headers' => [
        'Content-Type' => 'application/x-www-form-urlencoded',
      ]
      ];
      public $responseType = 'fullResponse';
      protected $responseTypes = ['fullResponse', 'body', 'json', 'jsonObject'];
    private $response = null;

    //Setter and Getter: Uri
    public function __construct(string $uri = '') {
      $this->uri = $uri;
    }

    public function setUri(string $uri) {
      $this->uri = $uri;
      return $this;
    }
    
    public function getUri():string {
     return $this->uri; 
    }

    //Setter and Getter: Method
    public function setMethod(string $method) {
      $this->method = $method;
      return $this;
    }

    public function getMethod():string {
      return $this->method;
    }

    //Setter: FormParams
    public function setFormParams(array $formParams):object
    {
        $this->options = array_merge($this->options, ['form_params' => $formParams]);
        return $this;
    }

    public function setAuthorization(string $authorization):object {
      $this->options = array_merge($this->options['headers'], ['Authorization' => $authorization]);
      return $this;
    }



    public function setResponse(string $responseType):object {
      if( !in_array($responseType, $this->responseTypes) ) throw new \Exception("$responseType is not a valid response type");
      $this->responseType = $responseType;
      return $this;
    }

    public function setOptions(array $options):object {
      $this->options = array_merge($this->options, $options);
      return $this;
    }

    public function getOptions():array
    {
        return $this->options;
    }

    public function getClient():object
    {
        if (is_null($this->client)) {
            $this->client = new GuzzleClient();
        }
        return $this->client;
    }

    public function request() {
      return $this->getResponse();
    }

    public function getResponse(string $responseType = null) {

      if(is_string($responseType)) {
        $this->setResponse($responseType);
      }

      if (is_null($this->response)) {
        $this->response =  $this->getClient()->request($this->getMethod(), $this->getUri(), $this->getOptions());
      }


      switch($this->responseType) {
        case 'fullResponse': 
          return $this->response;
        break;
        case 'body': 
          $body = $this->response->getBody();
          return (string) $body;
        break;
        case 'json': 
          $body = $this->response->getBody();
          return json_decode((string) $body, true);
        break;
        case 'jsonObject': 
          $body = $this->response->getBody();
          return json_decode((string) $body, false);
        break;
      }
    }
}

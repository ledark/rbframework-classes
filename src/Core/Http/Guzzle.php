<?php

namespace RBFrameworks\Core\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Cookie\SessionCookieJar;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Config;

class Guzzle {

    public $client = null;
    public $headers = [];

    /**
     * Por padrão, a uri é usada apenas como sufixo para Config::get('server.guzzle_preferences.base_url')
     * exemplo de uso:
     * $response = (new Guzzle())->get('uri'); //return array if json or string if body
     * $response = (new Guzzle())->post('uri', []); //return array if json or string if body
     * $response = Guzzle::doGet('uri'); //return array if json or string if body
     * $response = Guzzle::doPost('uri', []); //return array if json or string if body
     */
    public function __construct(array $options = []) {
        $this->client = new Client(array_merge($options, ['cookies' => Config::assigned('server.guzzle_preferences.cookies', true)]));
    }

    /**
     * addHeader function example: (new Guzzle())->addHeader('Cookie', 'PHPSESSID=24f192f01f9bcabf14c0b8c1b55fe296');
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function addHeader(string $name, string $value) {
        $this->headers[$name] = $value;
    }

    public function getRequest(string $method, string $url, array $headers = [], $body = null) {
        $mountedUrl = Config::assigned('server.guzzle_preferences.base_url', '').$url;
        $headers = array_merge(Config::assigned('server.guzzle_preferences.headers', []), $this->headers, $headers);
        return new Request($method, $mountedUrl, $headers, $body);
    }

    public static function doGet(string $uri, array $headers = [], array $options = []) {
        $client = new self($options);
        foreach($headers as $name => $value) {
            $client->addHeader($name, $value);
        }
        return $client->get($uri);
    }

    public static function doPost(string $uri, array $data, array $headers = [], array $options = []) {
        $client = new self($options);
        foreach($headers as $name => $value) {
            $client->addHeader($name, $value);
        }        
        return $client->post($uri, $data);
    }

    public function get(string $uri) {
        $request = $this->getRequest('GET', $uri, $this->headers);
        $res = $this->client->sendAsync($request)->wait();
        $contentBody = $res->getBody()->getContents();
        if(is_null($contentBody)) {
            throw new \Exception('Content Body is null');
        }
        $contentJson = json_decode($contentBody, true);
        return is_array($contentJson) ? $contentJson : $contentBody;
    }

    public function post(string $uri, array $data, string $type = 'formParams') {
        switch($type) {
            case 'formData':
                return $this->postFormData($uri, $data);
            break;
            case 'formParams':
                return $this->postFormParams($uri, $data);
            break;
            case 'raw':
                return $this->postRaw($uri, $data);
            break;
            case 'binary':
                foreach($data as $file) {
                    return $this->postBinary($uri, $file);
                }
            break;
        }
    }

    public function postFormData(string $uri, array $data = []) {
        $multipart = [];
        foreach($data as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }
        $options = ['multipart' => $multipart];
        $request = $this->getRequest('POST', $uri, $this->headers);
        $res = $this->client->sendAsync($request, $options)->wait();
        $contentBody = $res->getBody()->getContents();
        if(is_null($contentBody)) {
            throw new \Exception('Content Body is null');
        }
        $contentJson = json_decode($contentBody, true);
        return is_array($contentJson) ? $contentJson : $contentBody;        
    }

    public function postFormParams(string $uri, array $params = []) {
        $this->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        $options = ['form_params' => $params];
        $request = $this->getRequest('POST', $uri, $this->headers);
        $res = $this->client->sendAsync($request, $options)->wait();
        $contentBody = $res->getBody()->getContents();
        if(is_null($contentBody)) {
            throw new \Exception('Content Body is null');
        }
        $contentJson = json_decode($contentBody, true);
        return is_array($contentJson) ? $contentJson : $contentBody;             
    }

    public function postRaw(string $uri, $body = '') {
        if(is_array($body)) {
            $body = json_encode($body);
        }
        $request = $this->getRequest('POST', $uri, $this->headers, $body);
        $res = $this->client->sendAsync($request)->wait();
        $contentBody = $res->getBody()->getContents();
        if(is_null($contentBody)) {
            throw new \Exception('Content Body is null');
        }
        $contentJson = json_decode($contentBody, true);
        return is_array($contentJson) ? $contentJson : $contentBody;     
    }

    public function postBinary(string $uri, string $filepath) {
        if(!file_exists($filepath)) {
            throw new \Exception('File not found');
        }
        $this->addHeader('Content-Type', File::getMimeType($filepath));
        $body = File::get_file_contents($filepath);
        $request = $this->getRequest('POST', $uri, $this->headers, $body);
        $res = $this->client->sendAsync($request)->wait();
        $contentBody = $res->getBody()->getContents();
        if(is_null($contentBody)) {
            throw new \Exception('Content Body is null');
        }
        $contentJson = json_decode($contentBody, true);
        return is_array($contentJson) ? $contentJson : $contentBody;     
    }

}
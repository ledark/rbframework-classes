<?php 

namespace RBFrameworks\Core\Tests;

use RBFrameworks\Core\Config;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

trait RequestTrait {

    private $client;
    private $session;

    private function getSession():SessionCookieJar {
        if(!isset($this->session) or is_null($this->session)) {
            $this->session = new SessionCookieJar('PHPSESSID', true);
        }
        return $this->session;
    }

    private function getClient():Client {
        if(!isset($this->client) or is_null($this->client)) {
            $this->client = new Client([
                'base_uri' => Config::get('server.base_uri'),
                'timeout'  => 10.0,
                'cookies' => $this->getSession(),
                'allow_redirects' => [
                    'max' => 60,        // allow at most 10 redirects.
                    'strict' => false,      // use "strict" RFC compliant redirects.
                    'referer' => true,      // add a Referer header
                    'protocols' => ['http', 'https'], // only allow https URLs
                    'track_redirects' => true
                ],
            ]);
        }
        return $this->client;
    }

    public function getJson(string $uri):array {
        $request = new Request('GET', $uri);
        $response = $this->getClient()->send($request);
        $body = $response->getBody();
        $json = json_decode($body->getContents(), true);
        return $json;
    }

    public function getHtml($uri, string $return = 'contents') {
        $client = $this->getClient();
        $request = new Request('GET', $uri);
        $response = $client->send($request);
        if($return == 'headers') {
            return $response->getHeaders();
        } else
        if($return == 'response') {
            return $response;
        } else
        if($return == 'contents') {
            $body = $response->getBody();
            $html = $body->getContents();
            return $html;
        }
    }
    
    public function postJson($uri, $data, $return = 'array') {
        $client = $this->getClient();
        $request = new Request('POST', $uri, [
            'Content-Type' => 'application/json;charset=utf-8'
        ], json_encode($data));
        $response = $client->send($request);
        $body = $response->getBody();
        if($return == 'array') {
            $json = json_decode($body->getContents(), true);
            return $json;
        } else {
            return $body->getContents();
        }
    }
    
    public function postData($uri, $data, $return = 'array') {
        $client = $this->getClient();
        $request = new Request('POST', $uri, [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ], http_build_query($data));
        $response = $client->send($request);
        $body = $response->getBody();
        if($return == 'array') {
            $json = json_decode($body->getContents(), true);
            return $json;
        } else {
            return $body->getContents();
        }
    }
    
    public function doLogin() {
        $this->postJson(Config::get('tests.admin.uri'), [
            'login' => Config::get('tests.admin.login'),
            'senha' => Config::get('tests.admin.senha'),
        ]);
        return $this->getHtml('admin-home');
    }

}
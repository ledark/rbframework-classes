<?php

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Cookie\SessionCookieJar;

use RBFrameworks\Core\Http\Guzzle;

class HttpTest extends TestCase {

    public $client = null;

    private function getBaseUri() {
        return 'http://www.ad369.com.br/projetos/wvferramentas/';
    }

    private function getClient() {
        //new SessionCookieJar('PHPSESSID', true);        
        if(!$this->client) {
            $this->client = new Client(['cookies' => true]);
        }
        return $this->client;
    }

    private function getContents(string $urlSufix) {
        $request = new Request('GET', $this->getBaseUri().$urlSufix);
        $res = $this->getClient()->sendAsync($request)->wait();
        return $res->getBody()->getContents();
    }

    private function assertContentHasSessionKey(string $urlSufix, string $key) {
        $body = $this->getContents($urlSufix);
        $this->assertIsString($body);
        $body = json_decode($body, true);
        $this->assertArrayHasKey('session', $body);
        $this->assertArrayHasKey($key, $body['session']);
    }

    private function assertContentNotHasSessionKey(string $urlSufix, string $key) {
        $body = $this->getContents($urlSufix);
        $this->assertIsString($body);
        $body = json_decode($body, true);
        $this->assertArrayHasKey('session', $body);
        $this->assertArrayNotHasKey($key, $body['session']);
    }

    private function assertContentHasKey(string $urlSufix, string $key) {
        $body = $this->getContents($urlSufix);
        $this->assertIsString($body);
        $body = json_decode($body, true);
        $this->assertArrayHasKey($key, $body);
    }

    /** this Hits mantain the Session */
    public function testAsGuzzle() {
        $this->assertContentHasSessionKey('api/debug/start', 'LAST_ACTIVITY');
        $this->assertContentNotHasSessionKey('api/debug/start', 'here');
        $this->assertContentHasKey('api/banners/home', 'sample');
        $this->assertContentHasSessionKey('api/debug/end', 'LAST_ACTIVITY');
        $this->assertContentHasSessionKey('api/debug/end', 'here');
    }

    public function testAsHttpCore() {
        $client = new Guzzle();

        $body = $client->get($this->getBaseUri().'api/debug/start');
        $this->assertArrayHasKey('session', $body);
        $this->assertArrayHasKey('LAST_ACTIVITY', $body['session']);
        $this->assertArrayNotHasKey('here', $body['session']);
        unset($body);

        $body = $client->get($this->getBaseUri().'api/banners/home');
        $this->assertArrayHasKey('sample', $body);

        $body = $client->post($this->getBaseUri().'api/produtos/busca', ['macarrao' => 'ovos']);
        $this->assertArrayHasKey('input', $body[0]);
        $this->assertArrayHasKey('macarrao', $body[0]['input']);
        $this->assertEquals($body[0]['input']['macarrao'], 'ovos');

        $body = $client->get($this->getBaseUri().'api/debug/end');
        $this->assertArrayHasKey('session', $body);
        $this->assertArrayHasKey('LAST_ACTIVITY', $body['session']);
        $this->assertArrayHasKey('here', $body['session']);
        unset($body);

    }


}
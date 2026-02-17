<?php

namespace RBFrameworks;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;
/**
 * @usage 
 */
class Http {

    private static function getClient():Client {
        $session = new SessionCookieJar('PHPSESSID', true);
        return new Client([
            'base_uri' => collection('server.base_uri'),
            'timeout'  => 60.0,
            'cookies' => $session,
            'allow_redirects' => [
                'max' => 60,        // allow at most 10 redirects.
                'strict' => false,      // use "strict" RFC compliant redirects.
                'referer' => true,      // add a Referer header
                'protocols' => ['http', 'https'], // only allow https URLs
                'track_redirects' => true
            ],
            'headers' => [
                'Environment' => 'Development' // Add your custom header here
            ]
        ]);        
    }

    private static function getResponse(string $uri, string $method = 'GET', array $options = []):Response {
        $uri = ltrim($uri, '/');
        $request = new Request($method, $uri, $options['headers'] ?? [], $options['body'] ?? null);
        $response = self::getClient()->send($request);
        if($response->getStatusCode() >= 400) {
            throw new ClientException($response);
        }
        return $response;
    }

    public static function getStatus(string $uri):int {
        try {
            return self::getResponse($uri)->getStatusCode();
        } catch (ClientException $e) {
            return $e->getResponse()->getStatusCode();
        }
    }

    public static function getHeaders(string $uri):array {
        return self::getResponse($uri)->getHeaders();
    }

    public static function getJson(string $uri):array {
        return json_decode(self::getResponse($uri)->getBody()->getContents(), true);
    }

    public static function getHtml(string $uri):string {
        return self::getResponse($uri)->getBody()->getContents();
    }

    public static function postJson(string $uri, array $data, string $return = 'array'):array|string {
        try {
            if($return == 'array') {
                return json_decode(self::getResponse($uri, 'POST', ['json' => $data])->getBody()->getContents(), true);
            } else {
                return self::getResponse($uri, 'POST', ['json' => $data])->getBody()->getContents();
            }
        } catch (ClientException $e) {
            return json_decode($e->getResponse()->getBody()->getContents(), true);
        }
    }

    /*
    $postJson = function($uri, $data, $return = 'array') use ($client) {
        $uri = ltrim($uri, '/');
        $request = new GuzzleHttp\Psr7\Request('POST', $uri, [
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
    };
    */

}
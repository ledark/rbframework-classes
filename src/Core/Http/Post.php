<?php

namespace RBFrameworks\Core\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\SessionCookieJar as GuzzleSession;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

/**
 * @sample Post::getJson('uri', []);
 */
abstract class Post
{
    public static function getJson(string $uri, array $form_params):array {
      
      
        return (new Provider())
        ->setUri($uri)
        ->setMethod('POST')
        ->setFormParams($form_params)
        ->setResponse('json')
        ->request()
      ;
      

        $client = new GuzzleClient();
          
          $response = $client->post($uri, [
            'debug' => TRUE,
            //'body' => $payload,
            'form_params' => $form_params,
            'headers' => [
              'Content-Type' => 'application/x-www-form-urlencoded',
            ]
          ]);
          
          $body = $response->getBody();
          return json_decode((string) $body, true);
    }

}
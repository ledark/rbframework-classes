<?php

namespace RBFrameworks\Core\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\SessionCookieJar as GuzzleSession;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

/**
 * @sample Post::getJson('uri', []);
 */
abstract class Get
{
    public static function getJson(string $uri):array {
        return (new Provider())
          ->setUri($uri)
          ->setMethod('GET')
          ->getResponse('json')
        ;
    }

    public static function getBody(string $uri):string {
      return (new Provider())
      ->setUri($uri)
      ->setMethod('GET')
      ->getResponse('body')
    ;
    }

    public static function getResponse(string $uri) {
      return (new Provider())
      ->setUri($uri)
      ->setMethod('GET')
      ->getResponse('fullResponse')
    ;
    }

}
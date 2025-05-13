<?php 

namespace Framework\Traits;

use GuzzleHttp\Client;

trait HttpStaticTrait {
    public static function get(string $url, array $options = []): array {
        $client = new Client();
        $response = $client->request('GET', $url, $options);
        return [
            'status' => $response->getStatusCode(),
            'body' => $response->getBody()->getContents()
        ];
    }

    public static function getJsonResponse(string $url, array $options = []): array {
        $response = self::get($url, $options);
        return [
            'status' => $response['status'],
            'body' => json_decode($response['body'], true)
        ];
    }
}
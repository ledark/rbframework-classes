<?php

namespace RBFrameworks\Core\Tests;

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Http;

/**
 * @group Front
 */
class FrontCase extends CommonCase {

    protected const CORE_TESTS_WRITELOCKER = false;
    
    protected function setUp():void {
        parent::setUp();

        if(self::CORE_TESTS_WRITELOCKER) file_put_contents('testing.lock', 'LAST TESTING RUNNING:'.date('Y-m-d H:i:s'), FILE_APPEND);
    }
    
    public function getJson(string $uri, callable $callback = null) {
        $uri = Http::sanitizeUri($uri);
        $client = new Http($uri);
        return $client->getJsonResponse();
    }
    public function get(string $uri, callable $callback = null) {

        $uri = Http::sanitizeUri($uri);
        $client = new Http($uri);

        $client->getHttpResponse($callback);
    }
    
    protected function tearDown(): void {
        if(self::CORE_TESTS_WRITELOCKER) \unlink('testing.lock');
        parent::tearDown();
    }

}
<?php

namespace RBFrameworks\Core\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @group Session
 */
class SessionCase extends CommonCase {

    protected const CORE_TESTS_WRITELOCKER = false;

    protected function setUp() {
        parent::setUp();
        if(self::CORE_TESTS_WRITELOCKER) {
            file_put_contents('log/tests/session.lock', 'LAST TESTING RUNNING:'.date('Y-m-d H:i:s')."\r\n", FILE_APPEND);
        }
        if(!isset($_SESSION)) $_SESSION = [];
    }

    public function testSessionExists() {
        $this->assertIsArray($_SESSION);
    }

    protected function tearDown(): void {
        if(self::CORE_TESTS_WRITELOCKER) {
            file_put_contents('log/tests/session.lock', json_encode($_SESSION).'LAST TESTING RUNNING:'.date('Y-m-d H:i:s')."\r\n", FILE_APPEND);
        }
        parent::tearDown();
        if(isset($_SESSION)) unset($_SESSION);
    }

}
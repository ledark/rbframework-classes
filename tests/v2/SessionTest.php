<?php

use RBFrameworks\Core\Session;

class SessionV2Test extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        // Ensure session is started for tests
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
    }

    public function testConstructor()
    {
        $session = new Session();
        $this->assertInstanceOf(Session::class, $session);
    }

    public function testConstructorWithSessionId()
    {
        $session = new Session('custom_session_id');
        $this->assertInstanceOf(Session::class, $session);
    }

    public function testSetAndGet()
    {
        Session::set('test_key', 'test_value');
        $result = Session::get('test_key');
        $this->assertEquals('test_value', $result);
    }

    public function testGetAll()
    {
        Session::set('key1', 'value1');
        Session::set('key2', 'value2');
        $result = Session::get();
        $this->assertIsArray($result);
    }

    public function testGetWithStringParam()
    {
        Session::set('specific_key', 'specific_value');
        $result = Session::get('specific_key');
        $this->assertEquals('specific_value', $result);
    }

    public function testClear()
    {
        Session::set('key1', 'value1');
        Session::set('key2', 'value2');
        Session::clear();
        $result = Session::get();
        $this->assertIsArray($result);
        // After clear, the array might still have some default keys, so check specific keys are removed
        $this->assertArrayNotHasKey('key1', $result);
        $this->assertArrayNotHasKey('key2', $result);
    }

    public function testCreateSessionID()
    {
        $session = new Session();
        $session->createSessionID('my_custom_id');
        $this->assertEquals('my_custom_id', $session->session_id);
    }

    public function testSessionIdProperty()
    {
        $session = new Session();
        $this->assertNotNull($session->session_id);
    }
}

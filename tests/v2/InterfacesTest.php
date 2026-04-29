<?php

use RBFrameworks\Core\Interfaces\isCrudable;
use RBFrameworks\Core\Interfaces\isJsonResponse;
use RBFrameworks\Core\Interfaces\isResponse;
use RBFrameworks\Core\Interfaces\NotificationServiceInterface;

class InterfacesTest extends \PHPUnit\Framework\TestCase
{
    public function testIsCrudableExists()
    {
        $this->assertTrue(interface_exists(isCrudable::class));
    }

    public function testIsJsonResponseExists()
    {
        $this->assertTrue(interface_exists(isJsonResponse::class));
    }

    public function testIsResponseExists()
    {
        $this->assertTrue(interface_exists(isResponse::class));
    }

    public function testNotificationServiceInterfaceExists()
    {
        $this->assertTrue(interface_exists(NotificationServiceInterface::class));
    }

    public function testIsCrudableMethods()
    {
        $methods = get_class_methods(isCrudable::class);
        $this->assertContains('create', $methods);
        $this->assertContains('read', $methods);
        $this->assertContains('update', $methods);
        $this->assertContains('delete', $methods);
    }
}

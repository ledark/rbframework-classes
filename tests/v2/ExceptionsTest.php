<?php

use RBFrameworks\Core\Exceptions\DefaultException;
use RBFrameworks\Core\Exceptions\AppException;
use RBFrameworks\Core\Exceptions\ClassNotFoundException;
use RBFrameworks\Core\Exceptions\CollectionException;
use RBFrameworks\Core\Exceptions\CoreTypeException;
use RBFrameworks\Core\Exceptions\DatabaseException;

class ExceptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultException()
    {
        $exception = new DefaultException('test message');
        $this->assertInstanceOf(DefaultException::class, $exception);
        $this->assertEquals('test message', $exception->getMessage());
    }

    public function testAppException()
    {
        $exception = new AppException('app error');
        $this->assertInstanceOf(AppException::class, $exception);
        $this->assertInstanceOf(DefaultException::class, $exception);
    }

    public function testClassNotFoundException()
    {
        $exception = new ClassNotFoundException('class not found');
        $this->assertInstanceOf(ClassNotFoundException::class, $exception);
        $this->assertInstanceOf(DefaultException::class, $exception);
    }

    public function testCollectionException()
    {
        $exception = new CollectionException('collection error');
        $this->assertInstanceOf(CollectionException::class, $exception);
        $this->assertInstanceOf(DefaultException::class, $exception);
    }

    public function testCoreTypeException()
    {
        $exception = new CoreTypeException('type error');
        $this->assertInstanceOf(CoreTypeException::class, $exception);
        $this->assertInstanceOf(DefaultException::class, $exception);
    }

    public function testDatabaseException()
    {
        $exception = new DatabaseException('db error');
        $this->assertInstanceOf(DatabaseException::class, $exception);
        $this->assertInstanceOf(DefaultException::class, $exception);
    }

    public function testExceptionInheritance()
    {
        $exception = new AppException('test');
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}

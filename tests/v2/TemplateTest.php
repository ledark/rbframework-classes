<?php

use RBFrameworks\Core\Template;

class TemplateTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $template = new Template();
        $this->assertInstanceOf(Template::class, $template);
    }

    public function testConstructorWithString()
    {
        $template = new Template('<h1>Test</h1>');
        $this->assertInstanceOf(Template::class, $template);
    }

    public function testConstructorWithFile()
    {
        $template = new Template(__FILE__);
        $this->assertInstanceOf(Template::class, $template);
    }

    public function testBuild()
    {
        $template = new Template();
        $result = $template->build('<p>Content</p>');
        $this->assertInstanceOf(Template::class, $result);
    }

    public function testIsFileFalse()
    {
        $template = new Template('<p>String content</p>');
        $this->assertFalse($template->isFile());
    }

    public function testAddSearchFolder()
    {
        $template = new Template();
        $result = $template->addSearchFolder('/path/to/templates');
        $this->assertInstanceOf(Template::class, $result);
    }

    public function testAddSearchExtension()
    {
        $template = new Template();
        $result = $template->addSearchExtension('.tpl');
        $this->assertInstanceOf(Template::class, $result);
    }

    public function testSetVar()
    {
        $template = new Template();
        $template->setVar('key', 'value');
        $this->assertEquals('value', $template->getVar('key'));
    }

    public function testSetVars()
    {
        $template = new Template();
        $template->setVars(['a' => 1, 'b' => 2]);
        $this->assertEquals(1, $template->getVar('a'));
        $this->assertEquals(2, $template->getVar('b'));
    }

    public function testGetVarDefault()
    {
        $template = new Template();
        $this->assertNull($template->getVar('nonexistent'));
    }

    public function testHasVar()
    {
        $template = new Template();
        $template->setVar('test', 'value');
        $this->assertTrue($template->hasVar('test'));
    }

    public function testRemoveVar()
    {
        $template = new Template();
        $template->setVar('test', 'value');
        $template->removeVar('test');
        $this->assertFalse($template->hasVar('test'));
    }

    public function testSetPage()
    {
        $template = new Template();
        $result = $template->setPage('page_name');
        $this->assertInstanceOf(Template::class, $result);
    }

    public function testGetPage()
    {
        $template = new Template();
        $template->setPage('my_page');
        $this->assertEquals('my_page', $template->getPage());
    }
}

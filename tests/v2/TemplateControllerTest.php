<?php

use RBFrameworks\Core\TemplateController;

class TemplateControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $controller = new TemplateController('header', 'footer');
        $this->assertInstanceOf(TemplateController::class, $controller);
    }

    public function testSetPrefixContent()
    {
        $controller = new TemplateController('header', 'footer');
        $result = $controller->setPrefixContent('<div>');
        $this->assertInstanceOf(TemplateController::class, $result);
    }

    public function testSetSufixContent()
    {
        $controller = new TemplateController('header', 'footer');
        $result = $controller->setSufixContent('</div>');
        $this->assertInstanceOf(TemplateController::class, $result);
    }

    public function testSetTemplatePage()
    {
        $controller = new TemplateController('header', 'footer');
        $result = $controller->setTemplatePage('bootstrap5');
        $this->assertInstanceOf(TemplateController::class, $result);
    }

    public function testRenderPages()
    {
        $controller = new TemplateController('header', 'footer');
        $result = $controller->renderPages(['page1', 'page2']);
        $this->assertIsString($result);
    }

    public function testHeaderPageProperty()
    {
        $controller = new TemplateController('header', 'footer');
        $this->assertEquals('header', $controller->headerPage);
    }

    public function testFooterPageProperty()
    {
        $controller = new TemplateController('header', 'footer');
        $this->assertEquals('footer', $controller->footerPage);
    }

    public function testTemplatePageProperty()
    {
        $controller = new TemplateController('header', 'footer');
        $this->assertEquals('bootstrap5', $controller->templatePage);
    }
}

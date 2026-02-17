<?php 

namespace RBFrameworks\Core;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TemplateControllerTest extends TestCase {

    private const Template              = 'RBFrameworks\Core\Template';
    private const TemplateController    = 'RBFrameworks\Core\TemplateController' ;
    private const VarTrait              = 'RBFrameworks\Core\Templates\Traits\VarTrait';
    private const TemplateTrait         = 'RBFrameworks\Core\Templates\Traits\TemplateTrait';
    private const PageTrait             = 'RBFrameworks\Core\Templates\Traits\PageTrait';

    public function testClassInterface() {
        $Template = new ReflectionClass(self::Template);
        $TemplateController = new ReflectionClass(self::TemplateController);
        $this->assertTrue($TemplateController->isSubclassOf(self::Template));
        $this->assertTrue(in_array(self::VarTrait, $Template->getTraitNames()));
        $this->assertTrue(in_array(self::TemplateTrait, $Template->getTraitNames()));
        $this->assertTrue(in_array(self::PageTrait, $Template->getTraitNames()));
    }

}
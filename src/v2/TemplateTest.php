<?php 

namespace RBFrameworks\Core;

use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase {

    public function testEmptyTemplate() {
        $this->expectOutputString('any-value-without-template');
        (new Template('any-value-without-template'))->displayPage();
    }

    public function testFileTemplate() {
        $this->assertEquals('Teste Conteúdo #1', (new Template(__DIR__.'/Templates/Legacy/test-sample-1.tmpl'))->renderPage());
        $this->assertEquals('Teste Conteúdo #1', (new Template(__DIR__.'/Templates/Legacy/test-sample-1'))->renderPage());
        $this->assertEquals('Teste Conteúdo #1', (new Template('Templates/Legacy/test-sample-1'))->renderPage());
        $this->assertEquals('Teste Conteúdo #1', (new Template('Legacy/test-sample-1'))->addSearchFolder(__DIR__.'/Templates/')->build()->renderPage());
    }

    public function testContentFileInsideTemplate() {
        $this->assertEquals('<template>Teste Conteúdo #3</template>', (new Template(__DIR__.'/Templates/Legacy/test-sample-2'))->renderTemplate(__DIR__.'/Templates/Legacy/test-sample-3'));
    }
}
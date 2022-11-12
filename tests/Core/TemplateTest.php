<?php 

use PHPUnit\Framework\TestCase;
use RBFrameworks\Core\Template;
use RBFrameworks\Core\Templates\Custom\Template as TemplateCustom;
use RBFrameworks\Core\Templates\Bootstrapv5\Template as TemplateBs5;
use RBFrameworks\Core\Directory;
use RBFrameworks\Core\Config;

class TemplateTest extends TestCase
{
    public function testNamespaces() {

        $tmpl = new Template('bs5-boilerplate.html');
        $this->assertFalse($tmpl->isFile(), 'template not as file');

        $tmpl = new Template(__DIR__.'/../../src/Core/Templates/bs5-boilerplate.html');
        $this->assertTrue($tmpl->isFile(), 'template not as file');
    }
    public function testExtendindTemplate()
    {

        ob_start();
        (new TemplateBs5())->render(__DIR__.'/../Samples/my-custom-simple-page.php');
        $content = ob_get_clean();
        $this->assertStringContainsString('bootstrap', $content);
        $this->assertStringContainsString('this is my-custom-simple-page', $content);
        
    }

    public function testCustomTemplate() {
        ob_start();
        ////__DIR__.'/../Samples/my-custom-simple-page.php'
        (new TemplateCustom(__DIR__.'/../Samples/my-custom-template.html'))->setPage(__DIR__.'/../Samples/my-custom-simple-page.php')->render();
        $content = ob_get_clean();
        $this->assertStringContainsString('<head>...</head>', $content);
        $this->assertStringContainsString('this is my-custom-simple-page', $content);
    }

    public function testCustomTemplate2() {
        ob_start();
        ////__DIR__.'/../Samples/my-custom-simple-page.php'
        (new TemplateCustom(__DIR__.'/../Samples/my-custom-template.html'))->setVar(['content' => __DIR__.'/../Samples/my-custom-simple-page.php'])->render();
        $content = ob_get_clean();
        $this->assertStringContainsString('<head>...</head>', $content);
        $this->assertStringContainsString('this is my-custom-simple-page', $content);
    }

    public function tearDown() {
        if(is_dir(Config::get('location.cache.assets'))) {
            foreach (new DirectoryIterator(Config::get('location.cache.assets')) as $fileInfo) {
                if($fileInfo->isDot()) continue;
                unlink($fileInfo->getPathname());
            }
            Directory::rmdir(Config::get('location.cache.assets'));
        }
    }    

    /*
    public function testHello()
    {
        $_GET['name'] = 'Fabien';

        ob_start();
        include 'index.php';
        $content = ob_get_clean();

        $this->assertEquals('Hello Fabien', $content);
    }
    */
}
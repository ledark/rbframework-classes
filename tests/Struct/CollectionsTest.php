<?php 

use PHPUnit\Framework\TestCase;

use RBFrameworks\Core\Config;

class CollectionsTest extends TestCase
{

    private function assertConfig(string $collection, $expected) {
        $this->assertEquals(Config::get($collection), $expected);
        return $this;
    }

    private function assertConfigHas(string $collection, $expected) {
        $arr = Config::get($collection);
        $this->assertArrayHasKey($arr, $expected);
        return $this;
    }
    public function testConfigurations()
    {   

        $this
            ->assertConfig('auth.login_page', '/admin/login')
            ->assertConfig('session.admin.name', 'RBAuth3v3')
            ->assertConfig('modules.sample.config.defaults.color', 'blue')
            ->assertConfig('modules.sample.config.defaults.status', 99)
        ;



    }


    

}
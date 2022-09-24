<?php 

use PHPUnit\Framework\TestCase;

use RBFrameworks\Core\Assets;

class FilesTest extends TestCase
{
    private static function getPath(string $path): string {
        return __DIR__.'/../../'.$path;
    }
    private function assertPath(string $path) {
        //function r(string $path) { return __DIR__.'/../../'.$path; }
        $this->assertTrue(file_exists(self::getPath($path)), "struct not exists: {$path}");
        return $this;
    }
    public function notestFiles()
    {   
        $this
            
        ->assertPath('src/Core/')
            ->assertPath('src/Core/App/')
            ->assertPath('src/Core/Assets/')
            ->assertPath('src/Core/Auth/')
            
            ->assertPath('src/Core/Database/')
                ->assertPath('src/Core/Database/Doctrine/')
                ->assertPath('src/Core/Database/Legacy/')
                ->assertPath('src/Core/Database/Sql/')
                ->assertPath('src/Core/Database/Traits/')

            ->assertPath('src/Core/Exceptions/')
            ->assertPath('src/Core/Http/')
            ->assertPath('src/Core/Interfaces/')
            
            ->assertPath('src/Core/Notifications/')
            ->assertPath('src/Core/Notifications/Services/')

            ->assertPath('src/Core/Legacy/')
            ->assertPath('src/Core/Modulos/')

            ->assertPath('src/Core/Response/')
                ->assertPath('src/Core/Response/Mock/')

            ->assertPath('src/Core/Session/')

            ->assertPath('src/Core/Templates/')
                ->assertPath('src/Core/Templates/Bootstrapv5/')
                ->assertPath('src/Core/Templates/Legacy/')

            ->assertPath('src/Core/Tests/')
            ->assertPath('src/Core/Traits/')

            ->assertPath('src/Core/Types/')
                ->assertPath('src/Core/Types/Php/')
                ->assertPath('src/Core/Types/Sql/')

            ->assertPath('src/Core/Utils/')
                ->assertPath('src/Core/Utils/Arrays/')
                ->assertPath('src/Core/Utils/Datagrid/')
                ->assertPath('src/Core/Utils/Strings/')

            ->assertPath('src/Core/Validator/')
                ->assertPath('src/Core/Validator/Rules/')

        
            ->assertPath('collections/')
            ->assertPath('collections/auth.php')
            ->assertPath('collections/database.php')
            ->assertPath('collections/debug.php')
            ->assertPath('collections/location.php')
            ->assertPath('collections/modules.php')
            ->assertPath('collections/server.php')
            ->assertPath('collections/session.php')
            ->assertPath('collections/symfony.php')

        ->assertPath('_include.php')
        ->assertPath('.gitignore')
        ->assertPath('composer.json')
        ->assertPath('README.md')
        ;
    }

    public function testNamespaces2() {

        $type = Assets::contentType(self::getPath('composer.json'));

        $this->assertEquals('application/json', $type);

    }    

}
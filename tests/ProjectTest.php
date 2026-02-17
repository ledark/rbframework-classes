<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public function testProject()
    {
        $projectRoot = __DIR__ . '/projeto-exemplo';
        $this->assertDirectoryExists($projectRoot);

        echo \is_testing();

        $container = function() use ($projectRoot) {
            ob_start();
            try {
                include $projectRoot . '/index.php';
            } catch(\Throwable $e) {
                throw $e;
            }
            return ob_get_clean();
        };

        $response = $container();
        $this->assertStringNotContainsString('ERROR', $response);
    }
}
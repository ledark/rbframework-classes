<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public function testProject()
    {
        $this->assertTrue(true);

        $projectRoot = __DIR__ . '/projeto-exemplo';
        $this->assertDirectoryExists($projectRoot);

        echo \is_testing();

        $container = function() use ($projectRoot) {
            $script = $projectRoot . '/index.php';

            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            $process = proc_open(
                escapeshellcmd(PHP_BINARY) . ' ' . escapeshellarg($script),
                $descriptors,
                $pipes,
                $projectRoot,
                array_merge($_ENV, [
                    'APP_ENV' => 'source',
                    'REQUEST_URI' => "/example",
                    'REQUEST_METHOD' => "GET",
                    'REMOTE_ADDR' => "0.0.0.1",
                ])
            );

            if (!is_resource($process)) {
                return false;
            }

            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            proc_close($process);

            return $output;
        };

        $response = $container();
        $this->assertStringNotContainsString('ERROR', $response);
    }
}
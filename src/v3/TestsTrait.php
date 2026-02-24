<?php

namespace RBFrameworks;

use DirectoryIterator;
use RBFrameworks\Core\Api;
use RBFrameworks\Core\Types\File;

trait TestsTrait {

    private static function buildMultipartBody(array $fields, array $files, string $boundary): string {
        $body = '';

        foreach ($fields as $name => $value) {
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Disposition: form-data; name=\"{$name}\"\r\n\r\n";
            $body .= $value . "\r\n";
        }

        foreach ($files as $name => $file) {
            $content = file_get_contents($file['path']);

            $body .= "--{$boundary}\r\n";
            $body .= "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$file['name']}\"\r\n";
            $body .= "Content-Type: {$file['type']}\r\n\r\n";
            $body .= $content . "\r\n";
        }

        $body .= "--{$boundary}--\r\n";

        return $body;
    }

    public static function runContainerMultipart(
        string $uri,
        array $fields = [],
        array $files = []
    ): string {

        $entryFile = 'index.php';

        $boundary = '----php-test-' . md5(uniqid('', true));
        $body     = self::buildMultipartBody($fields, $files, $boundary);

        $bootstrap = tempnam(sys_get_temp_dir(), 'php_container_') . '.php';

        file_put_contents($bootstrap, '<?php '
            . '$_SERVER = unserialize(base64_decode($argv[1]));'
            . '$body = base64_decode($argv[2]);'
            . '$stream = fopen("php://temp", "r+");'
            . 'fwrite($stream, $body);'
            . 'rewind($stream);'
            . 'fclose(STDIN);'
            . 'define("STDIN", $stream);'
            . 'include $_SERVER["SCRIPT_FILENAME"];'
        );

        $server = [
            'REQUEST_URI'     => $uri,
            'REQUEST_METHOD'  => 'POST',
            'CONTENT_TYPE'    => "multipart/form-data; boundary={$boundary}",
            'CONTENT_LENGTH'  => strlen($body),
            'SCRIPT_FILENAME' => realpath($entryFile),
            'DOCUMENT_ROOT'   => dirname(realpath($entryFile)),
        ];

        $cmd = sprintf(
            'php %s %s %s',
            escapeshellarg($bootstrap),
            escapeshellarg(base64_encode(serialize($server))),
            escapeshellarg(base64_encode($body))
        );

        $output = shell_exec($cmd);

        @unlink($bootstrap);

        return $output ?? '';
    }

    private static function runContainer(string $uri = '/', array $env = []): string {
        $entryFile = 'index.php';
        $entryFile = realpath($entryFile);

        $server = array_merge([
            'REQUEST_URI'    => $uri,
            'REQUEST_METHOD' => 'GET',
            'SCRIPT_NAME'    => '/' . basename($entryFile),
            'SCRIPT_FILENAME'=> $entryFile,
            'DOCUMENT_ROOT'  => dirname($entryFile),
            'SERVER_NAME'    => 'localhost',
            'SERVER_PORT'    => 80,
            'HTTP_HOST'      => 'localhost',
        ], $env);

        $bootstrap = tempnam(sys_get_temp_dir(), 'php_container_') . '.php';

        file_put_contents($bootstrap, '<?php '
            . '$_SERVER = unserialize(base64_decode($argv[1]));'
            . 'chdir($_SERVER["DOCUMENT_ROOT"]);'
            . 'include $_SERVER["SCRIPT_FILENAME"];'
        );

        $cmd = sprintf(
            'php %s %s',
            escapeshellarg($bootstrap),
            escapeshellarg(base64_encode(serialize($server)))
        );

        $output = shell_exec($cmd);

        @unlink($bootstrap);

        return $output ?? '';
    }


    public function assertRequestContainsString(string $request, string $needle, string $message = ''): void {
        $requestContent = self::runContainer($request, []);
        $this->assertStringContainsString($needle, $requestContent, $message);
    }

    public function assertPostRequestContainsString(string $request, array $fields, string $needle, string $message = ''): void {
        $requestContent = self::runContainerMultipart($request, $fields);
        $this->assertStringContainsString($needle, $requestContent, $message);
    }

}
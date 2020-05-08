<?php 

namespace RBFrameworks\Storage;

class Files implements StorageInterface {

    public function exists(string $path): bool {
        return file_exists($path);
    }

    public function read(string $path): string {
        return file_get_contents($path);
    }

    public function remove(string $path): void {
        unlink($path);
    }

    public function write(string $path, string $data): void {
        file_put_contents($path, $dada);
    }

}
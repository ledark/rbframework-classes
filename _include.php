<?php

function collection(string $collectionName, $default = null) {
    $collectionPath = get_root_path("_app/collections/");

    // Verifica se é uma notação com ponto (ex: "server.uri")
    if (strpos($collectionName, '.') !== false) {
        $parts = explode('.', $collectionName);
        $fileName = array_shift($parts); // Primeiro elemento é o arquivo
        $filePath = $collectionPath . $fileName . '.php';

        if (!file_exists($filePath)) {
            return $default;
        }

        $data = require $filePath;

        // Navega pelos níveis do array usando os parts restantes
        foreach ($parts as $key) {
            if (!isset($data[$key])) {
                return $default;
            }
            $data = $data[$key];
        }

        return $data;
    }

    // Se não tem ponto, carrega o arquivo inteiro
    $filePath = $collectionPath . $collectionName . '.php';

    if (!file_exists($filePath)) {
        return $default;
    }

    return require $filePath;
}

function config(string $configName, $default = null, bool $overwrite = false) {
    $globalId = collection('server.project_name', 'Framework');
    if($overwrite) {
        $GLOBALS[$globalId][$configName] = $default;
        return $default;
    }
    if(isset($GLOBALS[$globalId][$configName])) {
        return $GLOBALS[$globalId][$configName];
    }
    $actual = collection($configName, $default);
    $GLOBALS[$globalId][$configName] = $actual;
    return $actual;
}

function load_function(string $functionName):void {
    RBFrameworks\Autoload::loadFunction($functionName);
}

function load_functions(array $functionNames):void {
    foreach ($functionNames as $functionName) {
        load_function($functionName);
    }
}

function is_developer():bool {
    return in_array($_SERVER['REMOTE_ADDR'], collection('server.developer_ip', []));
}

function is_testing():bool {
    return getenv('APP_ENV') === 'testing';
}
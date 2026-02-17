<?php

$host = "localhost";
$site = "http://{$host}";

return [
    'base_uri' => $site,
    'php' => [
        'version' => '8.5.3',
    ],
    'http_host' => $host,
    'force_https' => false,
    'developer_ip' => [
        '::1',
        '191.181.58.122',
    ],
];
<?php

class HttpCodes {
    public static array $codes = [        
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        307 => 'Redirecionamento temporário',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * Retrieve the HTTP status code description for a given HTTP status code.
     *
     * @param int $code The HTTP status code to retrieve the description for.
     * @return string The description for the given HTTP status code.
     */
    public static function get(int $code): string {
        return self::$codes[$code];
    }

    /**
     * Get an array of HTTP status codes to their descriptions.
     *
     * @return array HTTP status codes mapped to their descriptions.
     */
    public static function getCodes(): array {
        return self::$codes;
    }

    /**
     * Retrieve the HTTP status code corresponding to a given status text.
     *
     * @param string $searchTerm The status text to search for.
     * @return int The HTTP status code associated with the given status text, or false if not found.
     */

     public static function getCodeByName(string $searchTerm): int {
        foreach (self::$codes as $code => $text) {
            if (stripos($text, $searchTerm) !== false) {
                return $code;
            }
        }
        return false; // Retorna false se não encontrar o termo
    }



}
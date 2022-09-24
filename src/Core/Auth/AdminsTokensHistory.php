<?php

namespace RBFrameworks\Core\Auth;

use RBFrameworks\Core\Database;

class AdminsTokensHistory extends Database
{
    public function __construct(string $tabela = 'admins_tokens_history', array $model = [], $config = null)
    {
        if (!count($model)) {
            $model = [
            'id' =>        'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY',
            'token' =>     'VARCHAR(255) NOT NULL UNIQUE',
            'on' =>        'INT(10) UNSIGNED NULL DEFAULT NULL',
            'message' =>   'TEXT NULL DEFAULT NULL',
            'context' =>   'LONGTEXT NULL DEFAULT NULL',
        ];
        }
        parent::__construct($tabela, $model, $config);

    }
}
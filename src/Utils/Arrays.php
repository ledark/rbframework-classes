<?php 

namespace Framework\Utils;

class Arrays {
    public static function getValueByDotKey(string $key, array $data, $default = null, string $separator = '.') {
        // Se a chave for vazia ou se os dados não forem um array válido, retorna o valor padrão
        if (empty($key) || !is_array($data) || count($data) === 0) {
            return $default;
        }
    
        // Verifica se a chave contém o separador e precisa ser processada
        if (strpos($key, $separator) !== false) {
            $keys = explode($separator, $key);
    
            foreach ($keys as $innerKey) {
                // Verifica se o valor atual é um array antes de acessar a próxima chave
                if (!is_array($data) || !array_key_exists($innerKey, $data)) {
                    return $default;
                }
    
                $data = $data[$innerKey];
            }
    
            return $data;
        }
    
        // Caso contrário, retorna o valor do array diretamente ou o valor padrão se a chave não existir
        return $data[$key] ?? $default;
    }
    
}
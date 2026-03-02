<?php 

use Utils\Strings;

function encoding_fix($value) {

    $dict = config('charset');
    //$dict = encoding_reverse($dict);
    foreach($dict as $key => $replace) {
        if(is_string($value)) {
            $value = Strings::fix_encoding($value);
            $value = str_replace($key, $replace, $value);
            $value = Strings::remove_special_chars($value);

        } else
        if(is_array($value)){
            $value = encoding_fix_array($value);
        }
    }
    return $value;
}

function encoding_fix_array(array $array) {
    foreach($array as $key => $value) {
        $array[$key] = encoding_fix($value);
    }
    return $array;
}

//mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
function encoding($value, string $to = 'UTF-8', string $from = 'ISO-8859-1') {
    if(is_string($value) and trim($value) === '') {
        return $value;
    }
    if(is_string($value) and !mb_check_encoding($value, 'UTF-8')) {
        return mb_convert_encoding($value, $to, $from);
    } else
    if(is_string($value) and mb_check_encoding($value, 'UTF-8')) {
        return mb_convert_encoding($value, $from, $to);
    } else
    if(is_array($value)) {
        return encoding_array($value, $to, $from);
    }
    return $value;
};

function encoding_reverse($value, string $from = 'UTF-8', string $to = 'ISO-8859-1') {
    return encoding($value, $to, $from);
}

function encoding_array(array $array, string $to = 'UTF-8', string $from = 'ISO-8859-1') {
    foreach($array as $key => $value) {
        $array[$key] = encoding($value, $to, $from);
    }
    return $array;
};

function encoding_auto(string $string, bool $reverse = false):string {
    $detect = mb_detect_encoding($string, mb_list_encodings(), true);
    if($detect == 'UTF-8') {
        $to = 'ISO-8859-1';
        $from = 'UTF-8';
    } else {
        $to = 'UTF-8';
        $from = 'ISO-8859-1';
    }
    if($reverse) {
        return encoding($string, $from, $to);
    } else {
        return encoding($string, $to, $from);
    }
}

function autologin_hash(string $login, string $senha, int $cod = 5) {
    $login = substr(md5($login), 3, 5);
    $senha = substr(md5($senha), 5, 6);
    $random = substr(md5($cod), 7, 3);
    $letterStart = substr($login, 0, 1);
    $letterEnd = substr($login, 2, 1);
    return $login.$senha.$letterStart.$cod.$letterEnd.$random;
}

function autologin_extract_cod(string $hash):int {
    if(strlen($hash) < 17) {
        return 0;
    } else {
        return (int) substr($hash, 12, -4);
    }
}

/**
 * @function json_encode_safe to encode a value like array or object to a json string
 * @param mixed $value
 * @return string
 * @throws Exception
 */
function json_encode_safe($value) {
    $json = json_encode($value);
    if($json === false) {
        $value = encoding($value);
        $json = json_encode($value);
    }
    if($json === false) {
        $value = encoding_reverse($value);
        $json = json_encode($value);
    }
    if($json === false) {
        throw new Exception('Erro ao codificar JSON');
    }
    return $json;
}

/**
 * @function json_decode_safe to decode a json string to a value like array or object
 * @param string $json
 * @return mixed
 * @throws Exception
 */
function json_decode_safe($json, int $count = 0) {
    $count++;
    $value = json_decode($json, true);
    if($value === null) {
        $json = encoding_reverse($json);
        $value = json_decode($json, true);
    }
    if($value === null) {
        $json = encoding($json);
        $value = json_decode($json, true);
    }
    if($value === null) {
        $value = json_decode_safe_sanitize($json, $count);
    }
    if($value === null) {
        throw new Exception('Erro ao decodificar JSON');
    }
    return $value;
}

function json_decode_safe_sanitize($json, int $count = 0) {
    $count++;
    if(is_null($json) or is_string($json) and trim($json) === '') {
        return '';
    }

    if($count > 10) {
        $sanitized = stripslashes($json);
        $value = json_decode($sanitized, true, 512, JSON_INVALID_UTF8_IGNORE);

        return $value === null ? [] : $value;
    }

    $json = stripslashes($json);
    return json_decode_safe($json, $count);
}
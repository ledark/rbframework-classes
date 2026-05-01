<?php

function utf8_encode_deep(&$input) {
    if (is_string($input)) {
        $input = utf8_encode($input);
    } elseif (is_array($input)) {
        array_walk_recursive($input, function(&$item) {
            if (is_string($item)) {
                $item = utf8_encode($item);
            }
        });
    }
}

<?php

namespace Framework\Input;

trait GetFieldTrait {

public static function getFieldNumber(string $name, int $default = 0):int {
        $options = new Options();
        $options->default = $default;
        return (int) self::getField($name, $options);
    }

    public static function getFieldArray(string $name, array $default = []):array {
        $options = new Options();
        $options->default = $default;
        return (array) self::getField($name, $options);
    }

    public static function getFieldText(string $name, string $default = ''):string {
        $options = new Options();
        $options->default = $default;
        return (string) self::getField($name, $options);
    }

    public static function getFieldTextarea(string $name, string $default = ''):string {
        $options = new Options();
        $options->sanitize = false;
        $options->default = $default;
        return (string) self::getField($name, $options);
    }

    public static function getField(string $name, $optionsOrDefaultValue) {

        if(is_object($optionsOrDefaultValue)) {

            $inputUser = new self();

            if($optionsOrDefaultValue->getFromAnywhere) $inputUser->getFromAnywhere();
            if($optionsOrDefaultValue->decodeUTF8) $inputUser->decodeUTF8();
            if($optionsOrDefaultValue->sanitize) $inputUser->sanitize();

            $inputData = $inputUser->getResult();
            return isset($inputData[$name]) ? $inputData[$name] : $optionsOrDefaultValue->default;
        } else {
            $inputData = self::getInstance()->get();
            return isset($inputData[$name]) ? $inputData[$name] : $optionsOrDefaultValue;
        }
    }

    public function decodeUTF8():object {
        $this->data = encoding_reverse($this->data);
        return $this;
    }

    public function encodeUTF8():object {
        $this->data = encoding($this->data);
        return $this;
    }

    public function sanitize():object {
        return $this;
    }

    public function getResult():array {
        return $this->data;
    }

}
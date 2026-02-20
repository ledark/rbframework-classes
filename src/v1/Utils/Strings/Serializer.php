<?php 

namespace Framework\Utils\Strings;

/**
 * @usage: $mySerializedVar = (new Serializer($mixedValue))->in(); //return serialized value var
 * @usage: $myOriginalVar = (new Serializer($mySerializedVar))->out(); //return original mixed value var
 */

class Serializer {

    public $base64 = false;
    public $originalValue = null;

    public function __construct($mixedValue, bool $asBase64 = false) {
        $this->originalValue = $mixedValue;
        if($asBase64 == true) $this->asBase64();
    }

    public function asBase64():object {
        $this->base64 = true;
        return $this;
    }

    public function in() {
        $mixedValue = $this->originalValue;
        
        return $this->base64 ? base64_encode(serialize($mixedValue)) : serialize($mixedValue);
    }

    public function out() {
        $mixedValue = $this->originalValue;
        return $this->base64 ? unserialize(base64_decode($mixedValue)) : unserialize($mixedValue);
    }

}
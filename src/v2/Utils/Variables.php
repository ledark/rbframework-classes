<?php 
/*
Copyright (c) 2021 Ricardo Bermejo
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. Neither the name of copyright holders nor the names of its
   contributors may be used to endorse or promote products derived
   from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL COPYRIGHT HOLDERS OR CONTRIBUTORS
BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * @author   "Ricardo Bermejo" <ricardo@bermejo.com.br>
 * @package  Variables
 * @version  1.0
 * @license  Revised BSD
  */

namespace RBFrameworks\Core\Utils;

class Variables {

    public $variable;

    public function __construct($variable) {
        $this->variable = $variable;
    }

    /**
     * As the constructor is not go change anything, then your use is for the all public methods bellow;
     * This methods as a simple way to convert this variable in another variable.
     * Bellow is string, bool and integer
     * Needs: double, array, object, resource
     */

    /** get whatever as String */

    public function getString() {
        switch(gettype($this->variable)) {
            case "boolean":         return $this->get_boolean_as_String($this->variable); break;
            case "integer":         return $this->get_integer_as_String($this->variable); break;
            case "double":          return $this->get_double_as_String($this->variable); break;
            case "string":          return $this->get_string_as_String($this->variable); break;
            case "array":           return $this->get_array_as_String($this->variable); break;
            case "object":          return $this->get_object_as_String($this->variable); break;
            case "resource":        return $this->get_resource_as_String($this->variable); break;
            case "NULL":            return $this->get_NULL_as_String($this->variable); break;
            default:                return 'undefined';            break;
        }

    }

    public function getStringBadged():string {
        switch(gettype($this->variable)) {
            case "boolean":
                return $this->variable ? '<span class="badge badge-success">true</span>' : '<span class="badge badge-danger">false</span>';
            break;
            case "integer":         
                return $this->get_integer_as_String($this->variable); 
            break;
            case "double":          
                return $this->get_double_as_String($this->variable); 
            break;
            case "string":          
                return $this->get_string_as_String($this->variable); 
            break;
            case "array":           
                return $this->get_array_as_String($this->variable); 
            break;
            case "object":          
                return $this->get_object_as_String($this->variable); 
            break;
            case "resource":        
                return $this->get_resource_as_String($this->variable); 
            break;
            case "NULL":            
                return $this->get_NULL_as_String($this->variable); 
            break;
            default:                
                return 'undefined';            
            break;
        }
    }

    public function __toString() {
        return $this->getString();
    }

    public function get_boolean_as_String($value):string {
        return ($value) ? 'true': 'false';
    }
    public function get_integer_as_String($value):string {
        return (string) $value;
    }
    public function get_double_as_String($value):string {
        return number_format($value, 2, ',', '.');
    }
    public function get_string_as_String($value):string {
        return $value;
    }
    public function get_array_as_String($value):string {
        ob_start(); print_r($value); return ob_get_clean();
    }
    public function get_object_as_String($value):string {
        ob_start(); print_r($value); return ob_get_clean();
    }
    public function get_resource_as_String($value):string {
        ob_start(); print_r($value); return ob_get_clean();
    }
    public function get_NULL_as_String($value):string {
        return 'NULL';
    }

    /** get whatever as Bool */
    public function getBool():bool {
        switch(gettype($this->variable)) {
            case "boolean":         return $this->variable; break;
            case "integer":         return $this->get_integer_as_Bool($this->variable); break;
            case "double":          return $this->get_double_as_Bool($this->variable); break;
            case "string":          return $this->get_string_as_Bool($this->variable); break;
            case "array":           return false; break;
            case "object":          return false; break;
            case "resource":        return false; break;
            case "NULL":            return false; break;
            default:                return false; break;
        }
    }

    private function get_integer_as_Bool(int $value): bool {
        return $value > 0 ? true : false;
    }
    private function get_double_as_Bool(float $value): bool {
        return $value > 0 ? true : false;
    }
    private function get_string_as_Bool(string $value): bool {
        $value = trim($value);
        $value = strtolower($value);
        return in_array($value, ["true", "t", "yes", "y", "1", "sim", "ok"]) ? true : false;
    }
    /** get whatever as Int */
    public function getInt():int {
        switch(gettype($this->variable)) {
            case "boolean":         return $this->variable ? 1 : 0; break;
            case "integer":         return $this->variable; break;
            case "double":          return intval($this->variable); break;
            case "string":          return intval($this->variable); break;
            case "array":           return 0; break;
            case "object":          return 0; break;
            case "resource":        return 0; break;
            case "NULL":            return 0; break;
            default:                return 0; break;
        }
    }

    public function getArray():array {
        return [];
    }

    public function getObject():object {
        return new \StdClass();
    }

    public function getResource() {
        return 'resource';
    }

    public function getNull() {
        return null;
    }

}
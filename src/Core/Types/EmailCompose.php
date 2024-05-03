<?php 

namespace RBFrameworks\Core\Types;

use Exception;

/**
 * Not concluided and not tested
 */
class EmailCompose implements TypeInterface {

    private $errors = 0;
    private $throwException = true;
    private $block_separator = ',';
    private $_value;
    private $_isValid = false;

    /**
     * @var string possible values: 
     * example@ig.com.br
     * example@ig.com.br, example@uol.com.br
     * User Name <example@hotmail.com>
     * User Name <example@hotmail.com, example@gmailcom>
     * User Name <example@hotmail.com, example@gmailcom>, User Name2 <another@ig.com.br>
     */
    public function __construct(string $value, string $block_separator = ',', bool $throwException = true) {
        $this->throwException = $throwException;
        $this->block_separator = $block_separator;
        $this->validateOpenClose($value);
        $this->validateBlocks($value);        
        return $this->validate();
    }

    private function validateOpenClose(string $value):bool {
        if(strpos($value, '<') !== false and strpos($value, '>') !== false) {
            $open = substr_count($value, '<');
            $close = substr_count($value, '>');
            return $open == $close;
        }
        return true;
    }

    private function validateBlocks(string $value):bool {
        $blocks = explode($this->block_separator, $value);
        foreach($blocks as $block) {
            $block = trim($block);
            if(strpos($block, '<') !== false and strpos($block, '>') !== false) {
                $block = explode('<', $block);
                $block[1] = str_replace('>', '', $block[1]);
                $this->validateEmail($block[1]);
            } else {
                $this->validateEmail($block);
            }
        }
        return true;
    }

    private function validateEmail(string $value):bool {
        $value = trim($value);
        $value = strtolower($value);
        if(!ctype_graph($value)) {
            $this->errors++;
            return false;
        }
        if(strpos($value, '@') === false) {
            $this->errors++;
            return false;
        }
        if(strpos($value, '.') === false) {
            $this->errors++;
            return false;
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors++;
            return false;
        }
        return true;
    }

    private function validate() {
        if($this->errors > 0) {
            if($this->throwException) {
                throw new Exception("Email invalid format");
            } else {
                return false;
            }
        }
        return true;
    }

    public function __toString() {
        return $this->_value;
    }

    public function getValue():string {
        return $this->_value;
    }

    public function isValid():bool {
        return $this->_isValid;
    }

    public function getFormatted()
    {
        return $this->_value;
    }

    public function getNumber(): int
    {
        return strlen($this->_value);
    }

    public function getString(): string
    {
        return $this->_value;
    }


}
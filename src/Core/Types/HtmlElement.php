<?php 

namespace RBFrameworks\Core\Types;

/**
 * Use para criar qualquer elemento Html, por exemplo, para criar: <div class="text-center">sample</div>:
 * $element = new HtmlElement('sample', 'div', ['class' => 'text-center']); OR
 * $element = HtmlElement::create('sample', 'div', ['class' => 'text-center']);
 * 
 * Possibilidade de fazer Wrap, como: <div class="container">[element]</div>:
 * $element->wrapElement('div', ['class' => 'container']);
 * 
 * Possibilidade de colocar Inner Children, como: <(...)<div class="">sample</div>(...)/>:
 * $element->addChildElement('div', ['class' => '']);
 */
class HtmlElement implements TypeInterface {

    public $value;
    public $tagName = 'span';
    public $attributes = [];    

    public function __construct($value = null, string $tagName = 'span', array $attributes = []) {
        $this->value = $value;
        $this->tagName = $tagName;
        $this->attributes = $attributes;
    }

    public static function create($value = null, string $tagName = 'span', array $attributes = []) {
        return new HtmlElement($value, $tagName, $attributes);
    }

    public function wrapElement(string $tagName, array $attributes = []) {
        $actual = $this->getFormatted();
        $this->value = $actual;
        $this->tagName = $tagName;
        $this->attributes = $attributes;
    }

    public function addChildElement(string $tagName, array $attributes = []) {
        $this->value = new HtmlElement($this->value, $tagName, $attributes);
    }

    private function getAttributes():string {
        $attributes = '';
        foreach($this->attributes as $key => $value) {
            $attributes .= " {$key}=\"{$value}\"";
        }
        return $attributes;
    }

    public function getFormatted() {
        if(is_null($this->value)) {
            return "<{$this->tagName}{$this->getAttributes()}/>";
        }
        return "<{$this->tagName}{$this->getAttributes()}>{$this->value}</{$this->tagName}>";
    }

    public function getNumber():int {
        return (int)$this->value;
    }

    public function __toString():string {
        return $this->getFormatted();
    }

    public function getString():string {
        return $this->getFormatted();
    }

    public function getValue() {
        $this->value;
    }

    public function getShrinked() {
        return $this->getValue();
    }
    public function getHydrated() {
        return $this->getValue();
    }
  
}
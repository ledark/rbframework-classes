<?php 

namespace Framework\Utils\Htmx;

use Framework\Utils\Htmx\Helper\Location;
use Framework\Utils\Htmx\Helper\Strings;
use Framework\Utils\Htmx\Constants;
use Framework\Utils\Htmx\Helper\Component;

class HtmxComponent extends HtmxRouter {

    public string $name;
    public string $html;
    public array $replaces = [];

    public function __construct(string $html = '', string $name = null, array $replaces = [], array $injector = []) {
        $this->name = $name??basename(str_replace('\\', '/', get_class($this)));



        if(empty($html) and !is_null($name)) { //$html = '' | $name = 'component'
            $html = Component::get($this->name, $replaces, $injector);
        }
        if($html == $name and !is_null($name)) { //$html = 'component' | $name = 'component'
            $html = Component::get($this->name, $replaces, $injector);
        }


        //data-error-control="htmx-error"
/*
        if(Constants\Type::NOT_FOUND != Location::getTypeFromPath($this->name)) {
            $html = Location::getComponenentFromMixedFile($this->name)->getHtml();
        }
*/
        $this->html = $html;
        $this->replaces = array_merge($replaces, $injector);
    }

    public function getName(string $type = null):string {
        return match($type) {
            'camel' => Strings::toCamelCase($this->name),
            'kebab' => Strings::toKebabCase($this->name),
            'snake' => Strings::toSnakeCase($this->name),
            default => $this->name,
        };
    }

    public function getHtml():string {
        extract($this->replaces);
        $html = $this->html;
        foreach($this->replaces as $key => $value) {
            if($value instanceof HtmxComponent) {
                $value = $value->getHtml();
            } else
            if(is_array($value)) {
                $value = implode('', $value);
            } else
            if(is_object($value)) {
                $value = (string)$value;
            }
            if(is_null($value)) {
                $value = '';
            }
            $html = str_replace('{'.$key.'}', $value, $html);
        }
        return $html;
    }

    public function __toString() {
        return $this->getHtml();
    }

}

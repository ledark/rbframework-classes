<?php 

namespace Traits;

trait PropertyHandlerFlexible {

    /** auto set and get Propertys always
     *  Inspired by https://www.php.net/manual/en/language.oop5.overloading.php#object.get by Nanhe Kumar comment
     *  Created by Ricardo[at]Bermejo.com.br
     *  Example:
     *  
     * 
     * $s = new Employee(); //Class that traits PropertyHandlerFlexible
     * $$s->setEmail('nanhe.kumar@gmail.com');
     * echo $s->getName(); //Nanhe Kumar
     * echo $s->getEmail(); // nanhe.kumar@gmail.com
     * s->setName('Nanhe Kumar');
     * $s->setAge(10); //This is current setted anyway
    **/

    public $vars = [];
    
    public function __call($name, $arguments) {

        if(substr($name, 0, 3) == 'set') {
            $this->vars[ lcfirst(substr($name, 3)) ] = count($arguments[0] > 1) ? $arguments : $arguments[0];
            return $this;
        } else
        if(substr($name, 0, 3) == 'get') {
            return $this->vars[ lcfirst(substr($name, 3)) ] ?? null;
        }

    }

}
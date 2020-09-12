<?php 

namespace Traits;

trait PropertyHandler {

    /** auto set and get Propertys if they exists 
     *  From https://www.php.net/manual/en/language.oop5.overloading.php#object.get by Nanhe Kumar comment
     *  Example:
     *  
     * 
     * $s = new Employee(); //Class that traits PropertyHandler
     * $$s->setEmail('nanhe.kumar@gmail.com');
     * echo $s->getName(); //Nanhe Kumar
     * echo $s->getEmail(); // nanhe.kumar@gmail.com
     * s->setName('Nanhe Kumar');
     * $s->setAge(10); //Notice: Undefined property setAge in
    **/

    public function __call($name, $arguments) {
        $action = substr($name, 0, 3);
        switch ($action) {
            case 'get':
                $property = lcfirst(substr($name, 3));
                if(property_exists($this,$property)){
                    return $this->{$property};
                }else{
                    $trace = debug_backtrace();
                    trigger_error('Undefined property  ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
                    return null;
                }
                break;
            case 'set':
                $property = lcfirst(substr($name, 3));
                if(property_exists($this,$property)){
                    $this->{$property} = $arguments[0];
                    return $this;
                }else{
                    $trace = debug_backtrace();
                    trigger_error('Undefined property  ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
                    return null;
                }
               
                break;
            default :
                return FALSE;
        }
    }

}
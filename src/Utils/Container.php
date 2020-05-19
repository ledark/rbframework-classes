<?php

/**
 * Utiliza PSR-11
 * Criado por Ricardo[at]Bermejo based in this tutorial: https://code.tutsplus.com/tutorials/dependency-injection-huh--net-26903
 */

namespace RBFrameworks\Utils;

class Container {
    
    protected $registry = array();
 
    public function __set($name, $resolver)    {
        $this->registry[$name] = $resolver;
    }
 
    public function __get($name) {
        return $this->registry[$name]();
    }
    
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($name) {
        if($this->has($name)) {
            return $this->registry[$name];
        } else {
            throw new Exception('NotFoundExceptionInterface');
        }
        throw new Exception('ContainerExceptionInterface');
    }
    
    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */    
    public function has($name): bool {
        return isset($this->registry[$name]) ? true : false;
    }
    
}

<?php 

namespace RBFrameworks\Core\Traits;

trait ContaineredTrait {

    /**
     * Container where the properties are stored
     *
     * @var []
     */
    private $container = [];

    /**
     * Getter method
     *
     * @param string $name
     * @return mixed
     * @throws \UnexpectedValueException
     */
    public function __get($name) {
        if (!isset($this->container[$name])) {
            throw new \UnexpectedValueException("'{$name}' is not an property");
        }

        return $this->container[$name];
    }

    /**
     * Setter method
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function __set($name, $value) {
        $this->container[$name] = $value;
    }
}
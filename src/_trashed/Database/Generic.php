<?php

namespace RBFrameworks\Database;

trait Generic {
    

    
    public function setQuery(string $query):object {
        $this->query = $query;
        return $this;
    }
    
    public function execute():object {
        $this->statement = $this->prepare($this->query);
        $this->statement->execute();
        return $this;
    }
    
    

    
}

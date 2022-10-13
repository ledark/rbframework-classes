<?php

namespace RBFrameworks\Database;

trait Generic {
    
    public $preventExecute = false;
    private $debugDump = '';
    public $statementAttributes = [];
    public $statementValues = [];
    
    public function setQuery(string $query):object {
        $this->query = $query;
        return $this;
    }
    
    public function setValues(array $values) {
        $this->statementValues = $values;
        return $this;
    }
    
    public function setSAttribute(int $attribute, $mixed) {
        $this->statementAttributes[$attribute] = $mixed;
        return $this;
    }
    
    public function preventExecute(bool $override = true): object {
        $this->preventExecute = $override;
        return $this;
    }
    
    public function execute(array $prepareAttributes = []):object {
        $this->statement = $this->prepare($this->query, $prepareAttributes);
        foreach($this->statementAttributes as $attribute => $value) $this->statement->setAttribute($attribute, $value);
        foreach($this->statementValues as $attribute => $value) {
             $this->statement->bindValue($attribute+1, $value);
        }
        if($this->preventExecute) {
            
            //DebugDump
            ob_start();
            $this->statement->debugDumpParams();
            $this->debugDump.= ob_get_clean();
            
        } else {
            $this->statement->execute();
        }
        return $this;
    }
    
}

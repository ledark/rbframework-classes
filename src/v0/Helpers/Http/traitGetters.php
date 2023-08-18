<?php

namespace RBFrameworks\Helpers\Http;

trait traitGetters {
    
    public function getXml() {
        return simplexml_load_string($this->render(), null, LIBXML_NOCDATA);
    }
    public function getJson() {
        return json_decode($this->render(), true);
    }
    public function getResult() {
        return $this->result;
    }
    public function getResponse() {
        return $this->response;
    }
    public function getErrors() {
        return $this->errors;
    }
    
    public function returnInfo($info) {
        switch($info) {
            case TYPE_NONE:
                return $this;
            break;
            case 'result':
                return $this->getResult();
            break;
            case 'response':
                return $this->getResponse();
            break;
            case 'errors':
                return $this->getErrors();
            break;
        }
    }    
    
}

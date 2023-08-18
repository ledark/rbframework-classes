<?php 

namespace RBFrameworks\Core\Notifications\Services;

use RBFrameworks\Core\Interfaces\NotificationServiceInterface;
use RBFrameworks\Core\Debug;

class Service implements NotificationServiceInterface {


    public function send()
    {
        Debug::log('send', [], 'Notifications', 'NotificationService');
        throw new \Exception("Method not implemented");
    }
    
    public function getErrors():array
    {
        throw new \Exception("Method not implemented");
    }
    
    public function getTemplate():string
    {
        throw new \Exception("Method not implemented");
    }
    

}
<?php 

namespace RBFrameworks\Core\Notifications;

use RBFrameworks\Core\Interfaces\NotificationServiceInterface;

class Notification {

    private $notificationService;

    public function __construct(NotificationServiceInterface $notificationService) {
        $this->notificationService = $notificationService;
    }

    public function send() {
        $this->notificationService->send();
    }

    public function getService():NotificationServiceInterface {
        return $this->notificationService;
    }    

}
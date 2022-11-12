<?php 

namespace RBFrameworks\Core\Notifications;

use RBFrameworks\Core\Config;
use RBFrameworks\Core\Http\Get;
use RBFrameworks\Core\Exceptions\CollectionException as Exception;

class WirePusher {

    public $id = 'hsYJmppxq';
    public $title = 'Undefined Title';
    public $message = 'Mensagem de Teste';
    public $type = 'Default';
    public $action = 'http://rbframework.com.br';
    public $image_url;
    public $message_id = 1;

    public function __construct(string $message = "Test Message", string $title = "Undefined Title") {
        $this->title = $title;
        $this->message = $message;
    }

    private function checkID() {
        if(is_null($this->id)) {
            $this->id = Config::get('wirepusher.id');
        }        
        if(is_null($this->id)) throw new Exception("WirePusher ID not found. Please, check if this collection exists: [wirepusher.id]");
    }

    public function setID(string $Id):object {
        $this->id = $Id;
        return $this;
    }
    public function setTitle(string $Title):object {
        $this->title = $Title;
        return $this;
    }
    public function setMessage(string $Message):object {
        $this->message = $Message;
        return $this;
    }
    public function setType(string $Type):object {
        $this->type = $Type;
        return $this;
    }
    public function setAction(string $Action):object {
        $this->action = $Action;
        return $this;
    }
    public function setImage_url(string $Image_url):object {
        $this->image_url = $Image_url;
        return $this;
    }
    public function setMessageID(string $Message_id):object {
        $this->message_id = $Message_id;
        return $this;
    }

    private function clearAllParams() {
        $this->id = 'hsYJmppxq';
        $this->title = 'Undefined Title';
        $this->message = 'Mensagem de Teste';
        $this->type = '';
        $this->action = '';
        $this->image_url = '';
        $this->message_id = 0;
    }

    public function clear(int $message_id) {
        $this->clearAllParams();
        $this->type = 'wirepusher_clear_notification';
        $this->message_id = $message_id;
        $this->send();
    }

    public function clearAll() {
        $this->clearAllParams();
        $this->type = 'wirepusher_clear_all_notifications';
        $this->send();
    }

    public function send() {
        $this->checkID();
        $uri = 'https://wirepusher.com/send?';
        $params = [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'action' => $this->action,
            'image_url' => $this->image_url,
            'message_id' => $this->message_id,
        ];
        foreach ($params as $key => $value) {
            if(is_null($value)) continue;
            if(empty($value)) continue;
            if($key == 'message_id' and is_string($value)) continue;
            if($key == 'message_id' and $value == 0) continue;
            $uri .= $key . '=' . urlencode($value) . '&';
        }
        $uri = rtrim($uri, '&');
        Get::getResponse($uri);
    }
}

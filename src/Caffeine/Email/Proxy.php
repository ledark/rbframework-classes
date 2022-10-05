<?php

/**
 * Email\Proxy
 *
 * Simple proxy driver. It pass all emails to an event
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace RBFrameworks\Caffeine\Email;

use RBFrameworks\Caffeine\Event;

class Proxy implements Driver {

  protected $listener = 'core.email.proxy.send';

  public function onInit($options){
    if (!empty($options['hook'])) $this->listener = $options['hook'];
  }

  public function onSend(Envelope $envelope){
    return array_reduce( (array) Event::trigger($this->listener, $envelope), function($carry, $item) {
    	 if (is_bool($item)) $carry[] = $item;
    	 return $carry;
    }, []);
  }

}


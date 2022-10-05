<?php

/**
 * Email
 *
 * Send messages via Email services.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2016 - http://caffeina.com
 */

namespace RBFrameworks\Caffeine;

class Email {
  use Module, Events;

  protected static $driver,
                   $options,
                   $driver_name;

  public static function using($driver, $options = null){
    $class = 'Email\\'.ucfirst(strtolower($driver));
    if ( ! class_exists($class) ) throw new \Exception("[core.email] : $driver driver not found.");
    static::$driver_name = $driver;
    static::$options     = $options;
    static::$driver      = new $class;
    static::$driver->onInit($options);
  }

  public static function create($mail=[]){
    if (is_a($mail, 'Email\\Envelope')){
      return $mail;
    } else {
      return new Email\Envelope(array_merge([
        'to'          => false,
        'from'        => false,
        'cc'          => false,
        'bcc'         => false,
        'replyTo'     => false,
        'subject'     => false,
        'message'     => false,
        'attachments' => [],
      ], $mail));
    }
  }

  public static function send($mail){
    $envelope = static::create($mail);
    $results = (array) static::$driver->onSend($envelope);
    static::trigger('send', $envelope->to(), $envelope, static::$driver_name, $results);
    Event::trigger('core.email.send', $envelope->to(), $envelope, static::$driver_name, $results);
    return count($results) && array_reduce( $results, function($carry, $item) {
      return $carry && $item;
    }, true );
  }

}

Email::using('native');

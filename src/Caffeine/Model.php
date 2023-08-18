<?php

/**
 * Model class
 *
 * Base class for an ORM.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

namespace RBFrameworks\Caffeine;

abstract class Model {
    use Module, Persistence, Events;

    public static function where($where_sql = false, $params = [], $flush = false){
      $key   = static::persistenceOptions('key');

      return SQL::reduce("SELECT {$key} FROM " . static::persistenceOptions('table') . ($where_sql ? " where {$where_sql}" : ''), $params, function($results, $row) use ($key) {
           $results[] = static::load($row->{$key});
           return $results;
      }, []);
    }

    public static function count($where_sql = false, $params = []) {
      return (int) SQL::value('SELECT COUNT(1) FROM ' . static::persistenceOptions('table') . ($where_sql ? " where {$where_sql}" : ''), $params);
    }

    public static function all($page=1, $limit=-1){
      return static::where($limit < 1 ? "" : "1 limit {$limit} offset " . (max(1,$page)-1)*$limit);
    }

    public function primaryKey(){
      $key  = static::persistenceOptions('key');
      return $this->{$key};
    }

    public static function create($data){
      $tmp = new static;
      $data = (object)$data;
      foreach (array_keys(get_object_vars($tmp)) as $key) {
         if (isset($data->{$key})) $tmp->{$key} = $data->{$key};
      }
      $tmp->save();
      static::trigger('create',$tmp,$data);
      return $tmp;
    }

}

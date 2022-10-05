<?php

/**
 * Text
 *
 * A module of string related utility.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @copyright Caffeina srl - 2015 - http://caffeina.com
 */

namespace RBFrameworks\Caffeine;

class Text {
  use Module;

  /**
   * Fast string templating.
   * Uses a Twig-like syntax.
   *
   * @example
   *    echo Text::render('Your IP is : {{ server.REMOTE_HOST }}',array('server' => $_SERVER));
   *
   * @author Stefano Azzolini <stefano.azzolini@caffeinalab.com>
   * @access public
   * @static
   * @param mixed $t  The text template
   * @param mixed $v (default: null)  The array of values exposed in template.
   * @return string
  */
  public static function render($t,$v=null){
    if (Options::get('core.text.replace_empties', true)) {
      $replacer = function($c) use ($v){
        return Object::fetch(trim($c[1]), $v);
      };
    } else {
      $replacer = function($c) use ($v){
        return Object::fetch(trim($c[1]), $v) ?: $c[0];
      };
    }

    return preg_replace_callback("(\{\{([^}]+)\}\})S",$replacer,$t);
  }

  /**
   * Create a "slug", an url-safe sanitized string.
   *
   * @example
   *   echo Text::slugify("Thîs îs --- à vêry wrong séntènce!");
   *   // this-is-a-very-wrong-sentence
   *
   * @access public
   * @static
   * @param  string $text The text to slugify
   * @return string       The slug.
   */
  public static function slugify($text){
    return preg_replace(
      ['(\s+)','([^a-z0-9-])i','(-+)'],['-','','-'],
      strtolower(self::removeAccents($text)));
  }

  /**
   * Translit accented characters to neutral ones
   *
   * @example
   *   echo Text::removeAccents("Thîs îs à vêry wrong séntènce!");
   *   // This is a very wrong sentence!
   *
   * @access public
   * @static
   * @param  string $text The text to translit
   * @return string       The translited text
   */
  public static function removeAccents($text){
    static $diac;
    return strtr(
      utf8_decode($text),
      $diac ? $diac : $diac = utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
      'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
  }

  /**
   * Cut a string from the end of a substring to the start of another
   *
   * @example
   *   echo strcut("Name: Ethan Hunt; Role: Agent",'Name: ',';');
   *   // Ethan Hunt
   *
   * @param  string $text      The source text
   * @param  string $start_tag The starting substring
   * @param  string $end_tag   Ending substring, if omitted all remaining string is returned
   * @return string            The cutted string
   */
  public static function cut($text, $start_tag, $end_tag=null){
    $_s = strlen($start_tag) + strpos($text, $start_tag);
    return $end_tag ? substr($text, $_s, strpos($text,$end_tag,$_s)-$_s) : substr($text, $_s);
  }

} /* End of class */

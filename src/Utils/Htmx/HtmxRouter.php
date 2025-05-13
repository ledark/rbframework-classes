<?php

namespace Framework\Utils\Htmx;

use Framework\Config;
use Framework\Input as InputForm;
use Framework\Utils\Arrays;
use Framework\Utils\Htmx\Helper\Component;
use Framework\Utils\Htmx\Helper\Location;
use Framework\Utils\Htmx\Helper\Strings;
use Framework\Utils\Htmx\Constants;
use Framework\Session;

class HtmxRouter {

    /**
     * @route POST /htmx
     * @route GET /htmx
     * @utf8 true
     * @response html
     */
    public function index() {
        self::loadFromClass();
        self::loadFromAlias(); //Executa o componente através de ?name
        self::loadFromName(); //Executa o componente através de ?name
        self::loadFromText(); //Executa o texto passado por ?text
        self::loadFromSession(); //Mostra a variável presente em $_SESSION usando o dotNotation passado por ?session
        self::loadFromCollection(); //Mostra a variável presente em Config::get() usando o dotNotation passado por ?collection
        self::loadFromController(); //Executa o método do controller passado por ?controller usando o dotNotation
        self::loadFromControllers(); //Executa os métodos dos controllers passados por ?controllers usando o dotNotation separados por virgula
        self::loadFromNone(); //Executa o componente UndefinedComponent
        exit();
    }

    private static function loadFromAlias() {
        $arg = InputForm::getFieldText('alias', '');
        if(!empty($arg)) {
            $alias = Config::get('htmx.alias.'.$arg, null);
            if(!is_null($alias)) {
                echo Component::get($alias);
                exit();
            }
        }
    }

    private static function loadFromName() {
        $componentName = InputForm::getFieldText('name', '');
        if(!empty($componentName)) {
            echo Component::get();
            exit();
        }
    }
    private static function loadFromText() {
        $arg = InputForm::getFieldText('text', '');
        if(!empty($arg)) {
            echo $arg;
            exit();
        }
    }
    private static function loadFromController() {
        $arg = InputForm::getFieldText('controller', '');
        if(!empty($arg)) {
            $parts = explode('.', $arg);
            $method = array_pop($parts);
            $method = Strings::toCamelCase($method);
            foreach($parts as $key => $value) {
                $parts[$key] = Strings::toPascalCase($value);
            }
            $controller = implode('\\', $parts);

            $controller = new $controller();
            $result = $controller->$method();
            echo $result;
            exit();
        }
    }
    private static function loadFromControllers() {
        $arg = InputForm::getFieldText('controllers', '');
        if(!empty($arg)) {
            $controllers = explode(',', $arg);
            $result = '';
            foreach($controllers as $controller) {
                $parts = explode('.', $controller);
                $method = array_pop($parts);
                $method = Strings::toCamelCase($method);
                foreach($parts as $key => $value) {
                    $parts[$key] = Strings::toPascalCase($value);
                }
                $controller = implode('\\', $parts);

                $controller = new $controller();
                $result .= $controller->$method();
            }
            echo $result;
            exit();
        }
    }

    private static function loadFromSession() {
        new Session();
        $arg = InputForm::getFieldText('session', '');
        if(!empty($arg)) {
            $value = isset($_SESSION[$arg]) ? $_SESSION[$arg] : null;
            if(is_null($value)) {
                $value = Arrays::getValueByDotKey( $arg, $_SESSION);
            }
            if(is_null($value)) {
                echo '';
                exit();
            }
            if(!is_null($value)) {
                echo $value;
                exit();
            }
        }
    }

    private static function loadFromCollection() {
        $arg = InputForm::getFieldText('collection', '');
        if(!empty($arg)) {
            $value = Config::get($arg, null);
            if(is_array($value)) {
                echo json_encode($value);
            } else {
                echo $value;
            }
            exit();
        }
    }

    private static function loadFromClass() {
        $loadFromClass = InputForm::getFieldText('class', '');
        if(!empty($loadFromClass)) {
            $loadFromClass = str_replace('/', '\\', $loadFromClass);
            $component = new $loadFromClass();
        }
    }

    private static function loadFromNone() {
        echo Component::get('undefined-component');
        exit();
    }


}
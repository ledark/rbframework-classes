<?php 

namespace Framework\Router;

use Framework\Input;
use Framework\View\BladeOne;

class RouterExample {

    /**
     * @route GET test/css
     * @status 200
     * @response css
     **/
    public function test_css() {
        return "body { background-color: #f0f0f0; }";
    }
    /**
     * @route GET test/file
     * @status 200
     * @response file
     **/
    public function test_file() {
        return get_root_path()."tests/Framework/Router/Responses/ImageResponseTest.png";
    }
    /**
     * @route GET test/html
     * @status 200
     * @response html
     * @utf8 true
     **/
    public function test_html() {
        return "<h1>Hello World!</h1><strong>Código Html.</strong>";
    }

    /**
     * @route GET test/form
     * @status 200
     * @response html
     * @utf8 true
     **/
    public function test_htmlForm() {
        return BladeOne::renderBlade('RouterExampleAssets.hello', ['title' => 'ChromeTest',], ['views' => get_root_path().'_app/class/Framework/Router']);
    }

    /**
     * @route POST test/form
     * @status 200
     * @response html
     * @utf8 true
     **/
    public function test_htmlFormPost() {
        return BladeOne::renderBlade('RouterExampleAssets.hello', ['title' => 'ChromeTest:POST','postData' => $_POST], ['views' => get_root_path().'_app/class/Framework/Router']);
    }


    /**
     * @route GET test/image
     * @status 200
     * @response image
     **/
    public function test_image() {
        return get_root_path()."tests/Framework/Router/Responses/ImageResponseTest.png";
    }
    /**
     * @route GET test/javascript
     * @status 200
     * @response javascript
     **/
    public function test_javascr() {
        return "console.log('teste');";
    }
    /**
     * @route GET test/json
     * @status 200
     * @response json
     * @cache user
     **/
    public function test_json() {
        return [
            'teste' => 'Acentuação'
        ];
    }
    /**
     * @route GET test/redirect
     * @status 200
     * @response redirect
     **/
    public function test_redirec() {
        return "{httpSite}test/redirecionado";
    }

    /**
     * @route GET test/redirecionado
     * @status 200
     * @response text
     **/
    public function test_redirecionado() {
        return "{httpSite}redirecionado";
    }

    /**
     * @route GET test/text
     * @route GET testHello
     * @status 200
     * @response text
     * @cache all
     **/
    public function test_text() {
        return "Hello World!";
    }

    /**
     * @route POST test/request/json
     * @status 200
     * @response json
     * @utf8 false
     **/
    public function test_request_json() {
        return [
            'input_from' => Input::detectFrom(),
            'data' => null,
        ];
    }

}
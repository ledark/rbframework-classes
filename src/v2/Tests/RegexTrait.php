<?php 

namespace RBFrameworks\Core\Tests;

trait RegexTrait {

    /**
     * @example assertFormInput('username', 'text', $html);
     */
    public function assertFormInput(string $name, string $type, string $content) {

        switch($type) {
            case 'textarea':
                $re = '/name="'.$name.'"[\s\S]*?<\/textarea>/';
            break;
            case 'select':
                $re = '/name="'.$name.'"[\s\S]*?<\/select>/';
            break;
            default:
                $re = '/name="'.$name.'"[\s\S]*?type="'.$type.'"|type="'.$type.'"[\s\S]*?name="'.$name.'"/';
            break;
        }

        $this->assertMatchesRegularExpression($re, $content);
    }

}
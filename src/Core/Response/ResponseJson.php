<?php

namespace RBFrameworks\Core\Response;

use RBFrameworks\Core\Contracts\isResponse;
use RBFrameworks\Core\Contracts\isJsonResponse;
use RBFrameworks\Core\Plugin;

class ResponseJson implements isResponse, isJsonResponse
{
    public $dados = [];
    public $forceEncodeUTF8 = false;

    public function __construct(array $dados, bool $forceEncodeUTF8 = false) {
       Plugin::load("utf8_encode_deep");
        $this->dados = $dados;
        $this->forceEncodeUTF8 = $forceEncodeUTF8;
    }

    private function setJsonHeader() {
        if(!headers_sent()) header("Content-Type: application/json");
    }

    private function handleEncoding() {
        if(!$this->forceEncodeUTF8) {
            utf8_decode_deep($this->dados);
        }
        if($this->forceEncodeUTF8) {
            utf8_encode_deep($this->dados);
        }
    }

    public function flush():void {
        $this->setJsonHeader();
        $this->handleEncoding();
        echo json_encode($this->dados);
        exit();
    }

    public static function json(array $dados, bool $forceEncodeUTF8 = false):void {
        $response = new self($dados, $forceEncodeUTF8);
        $response->flush();
    }
}

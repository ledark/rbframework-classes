<?php

namespace RBFrameworks\Core\Response;

use RBFrameworks\Core\Interfaces\isResponse;
use RBFrameworks\Core\Interfaces\isJsonResponse;
use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Utils\Encoding;

class ResponseJson implements isResponse, isJsonResponse
{
    public $dados = [];
    public $forceEncodeUTF8 = false;

    public function __construct(array $dados, bool $forceEncodeUTF8 = false) {
        $this->dados = $dados;
        $this->forceEncodeUTF8 = $forceEncodeUTF8;
    }

    private function setJsonHeader() {
        if(!headers_sent()) header("Content-Type: application/json");
    }

    private function handleEncoding() {
        if(!$this->forceEncodeUTF8) {
            Encoding::DeepDecode($this->dados);
        }
        if($this->forceEncodeUTF8) {
            Encoding::DeepEncode($this->dados);
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

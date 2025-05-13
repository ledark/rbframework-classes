<?php 

namespace Framework;

use GuzzleHttp\Client;
use Framework\Traits\HttpStaticTrait;

class Http {

    public $client;

    public function __construct() {
        $this->client = new Client();
    }

    use HttpStaticTrait;

}
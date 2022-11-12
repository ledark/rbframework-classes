<?php

namespace RBFrameworks\Core\Session;

use RBFrameworks\Core\Session\Message;
use RBFrameworks\Core\Http;

trait MessageTrait
{
    public static function doMsg() {
        $Msg = new Message();
        $Msg->render();
    }

    public static function doMsgError(string $message, string $redir = null) {
        (new Message())
            ->prepare()
            ->setMessage($message)
            ->setPrefix('<div class="alert alert-danger">')
            ->setSufix('</div>')
            ->setCssClass('')
        ;
        if(!is_null($redir)) {
            Http::redir($redir);
        }
    }

    public static function doMsgSuccess(string $message, string $redir = null) {
        (new Message())
            ->prepare()
            ->setMessage($message)
            ->setPrefix('<div class="alert alert-success">')
            ->setSufix('</div>')
            ->setCssClass('')
        ;
        if(!is_null($redir)) {
            Http::redir($redir);
        }
    }

}
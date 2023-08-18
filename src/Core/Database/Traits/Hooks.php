<?php 

namespace RBFrameworks\Core\Database\Traits;

trait Hooks {
    public function pre_parse():callable {
        return function($hash) {
            \RBFrameworks\Core\Debug::log('pre_parse', $hash, 'MeekroDB.PreParse', 'MeekroDB');
        };
    }
    public function pre_run():callable {
        return function($hash) {
            //\Core\Debug::log('pre_run', $hash, 'MeekroDB.PreRun', 'MeekroDB');
        };
    }
    public function post_run():callable {
        return function($hash) {
            //\Core\Debug::log('post_run', $hash, 'MeekroDB.PostRun', 'MeekroDB');
        };
    }
    public function run_success():callable {
        return function($hash) {
            \RBFrameworks\Core\Debug::log('run_success', $hash, 'MeekroDB.RunSuccess', 'MeekroDB');
        };
    }
    public function run_failed():callable {
        return function($hash) {
            \RBFrameworks\Core\Debug::log('run_failed', $hash, 'MeekroDB.RunFailed', 'MeekroDB');
        };
    }
}
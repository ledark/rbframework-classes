<?php 

namespace Framework\Database\Traits;

use Framework\Debug;
use Framework\Config;

trait MeekroTrait {
    private function resolveMeekroDB()
    {
        try {            
            $this->meekrodb->error_handler = false; // disable standard error handler
            $this->meekrodb->nonsql_error_handler = false; // disable standard error handler
            $this->meekrodb->throw_exception_on_error = true; // throw exception on mysql query errors
            $this->meekrodb->throw_exception_on_nonsql_error = true; // throw exception on library errors (bad syntax, etc)        
        } catch (\Exception $e) {
            Debug::log($e->getMessage(), [], 'MeekroDB.Exception','MeekroDB');
        }    
     }


     public function defaultHandlers(string $behavior = ''): object
     {
         try {
 
             if (in_array($behavior, ['logAll', 'logSuccess']) === true) {
                 $this->meekrodb->error_handler  = function ($params) {
                     Debug::log("SUCCESS " . $params['query'] . " run in " . $params['runtime'] . " (milliseconds)", [], 'MeekroDB.Success', 'MeekroDB');
                 };
             }
             if (in_array($behavior, ['logAll', 'logError']) === true) {
                 $this->meekrodb->error_handler = function ($params, $prefix = "") {
                     Debug::log("ERROR " . $params['query'] . " run in " . $params['runtime'] . " (milliseconds)", [], 'MeekroDB.Errors', 'MeekroDB');
                 };
             }
         } catch (\Exception $e) {
             Debug::log($e->getMessage(), [], 'MeekroDB.Exception','MeekroDB');
         }
         return $this;
     }     

}
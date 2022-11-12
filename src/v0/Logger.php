<?php

namespace RBFrameworks;

class Logger {
    
    public static $err404 = 'log/error/httpResponses';
    
    public static function res200(string $httpurl) {
        $append = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : '';
        file_put_contents(self::$err404.$append, date('Y-m-d H:i:s [').$_SERVER['REMOTE_ADDR'].'] 200 '.$httpurl."\r\n", FILE_APPEND);
    }

    public static function err404(string $httpurl): void {
        $append = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : '';
        file_put_contents(self::$err404.$append, date('Y-m-d H:i:s [').$_SERVER['REMOTE_ADDR'].'] 404 '.$httpurl."\r\n", FILE_APPEND);
    }

    public static function err404_read(): string {
        $result = [];
        $contents = file(self::$err404);
        foreach($contents as $line) {
            $date = substr($line, 0, 19);
            $parts = substr($line, 21);
            $parts = explode(']', $parts);
            $addr = $parts[0];
            $parts = explode(' ', trim($parts[1]));
            $type = $parts[0];
            $uri = $parts[1];
            $result[$uri]['date'] = $date;
            $result[$uri]['addr'] = $addr;
            $result[$uri]['type'] = $type;
            $result[$uri]['count'] = isset($result[$uri]['count']) ? $result[$uri]['count']+1 : 1;
        }
        $table = '';
        echo '<table class="table">';
        foreach($result as $uri => $props) {
            if($props['type'] != '404') continue;
            echo "<tr>
                <td>{$uri} <span class=\"badge badge-danger\">{$props['count']}</span></td>
                <td>{$props['date']}</td>
                <td>{$props['addr']}</td>
                <td>{$props['type']}</td>
                
                </tr>
                ";
        }
        echo '</table>';
        
        return $table;
        
    }
    

    public static function res200_read(): string {
        $result = [];
        $contents = file(self::$err404);
        foreach($contents as $line) {
            $date = substr($line, 0, 19);
            $parts = substr($line, 21);
            $parts = explode(']', $parts);
            $addr = $parts[0];
            $parts = explode(' ', trim($parts[1]));
            $type = $parts[0];
            $uri = $parts[1];
            $count = isset($result[$uri]) ? $result[$uri]['count']+1 : 1;
            $result[$uri]['date'] = $date;
            $result[$uri]['addr'] = $addr;
            $result[$uri]['type'] = $type;
            $result[$uri]['count'] = isset($result[$uri]['count']) ? $result[$uri]['count']+1 : 1;
        }
        $table = '';
        echo '<table class="table">';
        foreach($result as $uri => $props) {
            if($props['type'] != '200') continue;
            echo "<tr>
                <td><a href=\"$uri\">{$uri}</a> <span class=\"badge badge-success\">{$props['count']}</span></td>
                <td>{$props['date']}</td>
                <td>{$props['addr']}</td>
                <td>{$props['type']}</td>
                
                </tr>
                ";
        }
        echo '</table>';
        
        return $table;
        
    }
    
}


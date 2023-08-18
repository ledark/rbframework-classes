<?php 

namespace RBFrameworks\Core\Utils;

use RBFrameworks\Core\Utils\Strings\Dispatcher;

abstract class ArraysTable {
        
    /**
     * Translate a result array into a HTML table
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.3.2
     * @link        http://aidanlister.com/repos/v/function.array2table.php
     * @param       array  $array      The result (numericaly keyed, associative inner) array.
     * @param       bool   $recursive  Recursively generate tables for multi-dimensional arrays
     * @param       string $null       String to output for blank cells
     */
    public static function array2table($array, $recursive = false, $null = '&nbsp;', $applyHtmlEntities = false) {
        // Sanity check
        if (empty($array) || !is_array($array)) {
            return false;
        }
        if (!isset($array[0]) || !is_array($array[0])) {
            $array = array($array);
        }
        // Start the table
        $table = "<table class=\"table\">\n";
        // The header
        $table .= "\t<tr>";
        // Take the keys from the first row as the headings
        foreach (array_keys($array[0]) as $heading) {
            $table .= '<th>' . $heading . '</th>';
        }
        $table .= "</tr>\n";
        // The body
        foreach ($array as $row) {
            $table .= "\t<tr>" ;
            foreach ($row as $cell) {
                $table .= '<td>';
                // Cast objects
                if (is_object($cell)) { $cell = (array) $cell; }
                
                if ($recursive === true && is_array($cell) && !empty($cell)) {
                    // Recursive mode
                    $table .= "\n" . self::array2table($cell, true, true) . "\n";
                } else {
                    
                    if($applyHtmlEntities) {
                        $table .= (strlen($cell) > 0) ? htmlspecialchars((string) $cell) : $null;
                    } else {
                        $table.= (strlen($cell) > 0) ? $cell : $null;
                    }
                }
                $table .= '</td>';
            }
            $table .= "</tr>\n";
        }
        $table .= '</table>';
        return $table;
    }


    function array2table_simple($p) {
        ob_start();
        $j = 0;
        echo '<table class="table">';
        foreach($p as $i => $r) {
            if($j == 0) {
                echo '<tr>';
                foreach($r as $n => $m) {
                    echo "<th>$n</th>";
                }
                echo '</tr>';
            }
            echo '<tr>';
            foreach($r as $n => $m) {
                echo "<td>$m</td>";
            }
            echo '</tr>';
            $j++;
        }
        echo '</table>';
        return ob_get_clean();
        
    }

    function array2table_ficha($p, $replaces = array()) {
        ob_start();
        echo '<table class="table">';
        foreach($p as $i => $r) {
            if(is_array($r)) {
                foreach($r as $n => $m) {
                    if(isset($replaces[$n])) $n = $replaces[$n]; else $n = Dispatcher::camelcased($n);
                    echo "
                    <tr>
                        <th>$n</th>
                        <td>$m</td>
                    </tr>
                    ";			
                }
            } else {
                if(isset($replaces[$i])) $i = $replaces[$i]; else $i = Dispatcher::camelcased($i);
                echo "
                    <tr>
                        <th>$i</th>
                        <td>$r</td>
                    </tr>
                    ";			            
            }
        }
        echo '</table>';  
        return ob_get_clean();
    }

    public static function table(array $data, array $columns = [], array $options = []):string {
        $options = array_merge([
            'table' => [
                'class' => 'table table-striped table-bordered table-hover table-sm',
            ],
            'thead' => [
                'class' => '',
            ],
            'tbody' => [
                'class' => '',
            ],
            'tr' => [
                'class' => '',
            ],
            'th' => [
                'class' => '',
            ],
            'td' => [
                'class' => '',
            ],
        ], $options);
        
        $table = '<table';
        foreach($options['table'] as $key => $value) {
            $table .= ' '.$key.'="'.$value.'"';
        }
        $table .= '>';
        
        $table .= '<thead';
        foreach($options['thead'] as $key => $value) {
            $table .= ' '.$key.'="'.$value.'"';
        }
        $table .= '>';
        
        $table .= '<tr';
        foreach($options['tr'] as $key => $value) {
            $table .= ' '.$key.'="'.$value.'"';
        }
        $table .= '>';
        
        foreach($columns as $column) {
            $table .= '<th';
            foreach($options['th'] as $key => $value) {
                $table .= ' '.$key.'="'.$value.'"';
            }
            $table .= '>';
            $table .= $column;
            $table .= '</th>';
        }
        
        $table .= '</tr>';
        $table .= '</thead>';
        
        $table .= '<tbody';
        foreach($options['tbody'] as $key => $value) {
            $table .= ' '.$key.'="'.$value.'"';
        }
        $table .= '>';
        
        foreach($data as $row) {
            $table .= '<tr';
            foreach($options['tr'] as $key => $value) {
                $table .= ' '.$key.'="'.$value.'"';
            }
            $table .= '>';
            
            foreach($columns as $column) {
                $table .= '<td';
                foreach($options['td'] as $key => $value) {
                    $table .= ' '.$key.'="'.$value.'"';
                }
                $table .= '>';
                $table .= $row[$column];
                $table .= '</td>';
            }

            $table .= '</tr>';
        }
        
        $table .= '</tbody>';
        $table .= '</table>';
        
        return $table;
    }
}
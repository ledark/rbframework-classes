<?php 

namespace RBFrameworks\Component;

class Html implements ComponentInterface {
    
    public function getSchema(): array {
        return [
            'config' => '<?php ?>',
            'html' => [
                'head' => [
                    'ini' => ['<?php ?>'],
                    'inn' => ['<?php ?>'],
                    'end' => ['<?php ?>'],
                ],
                'body' => [
                    'ini' => ['<?php ?>'],
                    'inn' => ['<?php ?>'],
                    'end' => ['<?php ?>'],
                ],
            ],
            'render' => ['<?php ?>'],
        ];        
    }

}
<?php

class HTML {
    
    public $schema = [
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
    
    public function setConfig(string $phpcode) {
        $this->schema['config'] = $phpcode;
    }
    
    public function setHtmlHead(string $phpcode, string $priority = 'inn') {
        $this->schema['html']['head'][$priority] = $phpcode;
    }
    
    public function setHtmlBody(string $phpcode, string $priority = 'inn') {
        $this->schema['html']['body'][$priority] = $phpcode;
    }
    
    public function setRender(string $phpcode) {
        $this->schema['render'] = $phpcode;
    }
    
    public function renderAll() {
        
    }
    
}
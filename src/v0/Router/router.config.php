<?php return [
    
    'route' => 'produtos/view/(\w)/(\w)',
    'matches' => ['id', 'name'],
    
    'dependences' => [
        
    ],
    
    'response' => [
        'code' => 200,
        'header' => ['Content-Type' => 'text/html'],
    ],
    
];



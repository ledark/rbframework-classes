<?php return [
    'ignore_groups' => [
		'assets_output_buffer',
        'MeekroDB.PreParse',
        'MeekroDB.RunSuccess',
    ],
    'ignore_filenames' => [
        'coreassetsrender',
    ],
    'ignore_contexts' => [
        function(array $context):bool { 
            //Return true se desejar que o Debug também seja ignorado. Normalmente após analisar alguma variável do context.
			if(isset($context['ignoreDebug']) and is_bool($context['ignoreDebug'])) return $context['ignoreDebug'];
            return false;
        },
    ],
];
# rbframework-classes
O propósito de projeto é uma refatoração completa do RBFrameworks, que atualmente está funcionando na versão 0.2.68 (fev/2026).

# ETAPA 1: Make Work

Todas as built-in funções estão no arquivo [_include.php] que se o composer estiver definido corretamente, deverá ser traziado automaticamente para o projeto.
A excessão é a função get_root_path() que deve ser definida por projeto. O ideal é que seja no arquivo _app/php_functions.php


## Definições e Termos
 - *Component* Classes chamam outras Classes em conjunto
 - *Service* Diversos Componentes Orquestrados por outra Classe
 - *Application* Conjunto de Serviços


## Install the old version util new is ready
Simple init this repo with composer:

```
composer install ledark/rbframeworks
```

## Notas da Refatoração
249 arquivos em 51 pastas na pasta princiap src/Core:

Core\Api                --Lida com [Responses]. Mas isso é claro?
└─── Api
└─── ApiProject
└─── App
└─── App92
└─── App93
└─── Assets
└─── Auth
└─── Cache
└─── Chance
└─── Config
└─── Database
└─── Debug
└─── Directory
└─── Http
└─── Imagem
└─── Input
└─── InputForm
└─── InputTrait
└─── InputUser
└─── InputUserOptions
└─── Modulo
└─── Modulos
└─── Plugin
└─── Response
└─── Session
└─── Template
└─── TemplateController
└─── TemplateControllerTest
└─── TemplateTest

Core\App                --Lida com App. Mas isso é claro?
Core\Assets             --Lida com [Vue].
Core\Auth
Core\Chance
Core\Database           --O mais inchado, com opções de Doctrine, Form, Legacy, Medoo, Meekro e antigos.
Core\Exceptions
Core\Http
Core\Interfaces
Core\Legacy
Core\Modulos
Core\Notifications      --Existems Services e Templates. Parece que não estão no lugar certo.
Core\Response           --Se existe algo em API, isso se torna confuso. Mas a ideia é boa.
Core\Session
Core\Symfony            -- Não faz sentido, ainda contem Templates nela. WTF?
Core\Templates          --Tem Snippts de Bootrapv5, mas parece que isso deveria não ser do framework e sim do projeto
Core\Tests
Core\Traits
Core\Types              -- Muita coisa poderia ser Types, e já tem [Form] [Php] [Sql]
Core\Utils              -- Coisas genérica de Arrays, Datagrid e Strings
Core\Validator          --Possui Rules, mas isso não é algo que conflita com o Auth?
# rbframework-classes
Classes for RBFrameworks and Utils

## Dependences
Depedends on Private Projects using RBFrameworks

## Install
Simple init this repo with composer:

```
composer install ledark/rbframeworks
```

## Usage as Utilities
```php
use RBFrameworks\Core\Utils\Strings\Dispatcher;

echo Dispatcher::sef("Uma frase que será transformada em versão Search Engine Friendly!"); //uma-frase-que-sera-transformada-em-versao-search-engine-friendly
```

# Folder Structure
```bash
.
│   src                  #source files .php maped to RBFrameworks\ namespace
│   └─── Database/
│   └─── Symfony/
│   └─── Types/
│   └─── Utils/
│   └─── App
│   └─── Database
│   └─── Debug
│   └─── Directory
│   └─── Http
│   └─── Plugin
│   └─── Template
│   └─── TemplateController
│   └─── _include.php
│   .gitignore           #keep files clean
│   composer.json        #composer
│   README.md            #this file
```

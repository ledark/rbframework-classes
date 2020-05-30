# rbframework-classes
Classes for RBFrameworks and Utils

## Dependences
Depedends on Private Projects using RBFrameworks

## Install
Simple init this repo with composer:

```
composer install ledark/rbframeworks
```

## Usage
```php
use RBFrameworks\Storage\Files as Files;

$meusArquivos = new Files();
```

# Folder Structure
```bash
.
??? src                  #source files .php maped to RBFrameworks\ namespace
?   ??? Storage
?   ?   ??? Files
?   ?   ??? Database
??? tests                #test files
?   ??? file21.ext       #dev
?   ??? file22.ext       #dev
?   ??? file23.ext       #dev
??? vendor               #composer
??? composer.json        #composer
??? README.md            #this file
```

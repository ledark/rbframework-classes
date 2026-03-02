Coloque arquivos nessa pasta e você pode chamar com:

```php
    Framework\Autoload::loadFunction('myFunction'); //or
    Framework\Autoload::loadFunctions(['myFunction1', 'myFunction2']); //or
    load_function("myFunction"); //or
    load_functions(['myFunction1', 'myFunction2']);
```
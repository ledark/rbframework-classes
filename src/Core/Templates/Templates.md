# RBFrameworksReadme

Uma forma rápida de criar o front-end com essa configuração:
- Bootstrap v.5.2.1
- Vue 3
- jQuery v3.5.1

```php
//in anywhere
use RBFrameworks\Core\Templates\Bootstrap5\Template;
(new Template())->render(__DIR__.'/my-page.php');
```

Se desejar um template personalizado, é possível estender essa classe da seguinte maneira:

```php
// myCustomTemplate.php

use RBFrameworks\Core\Templates\Bootstrap5\Template;

class myCustomTemplate extends Template {

    public function render(string $page) {
        echo $this->renderPage(__DIR__."/_head.php");
        echo $this->renderPage($page);
        echo $this->renderPage(__DIR__."/_foot.php");
        
    }
}

//in anywhere
(new myCustomTemplate())->render(__DIR__.'/my-page.php');
```

E finalmente, se desejar renderizar a [my-page.php] dentro de um template, é possível dessa forma, por exemplo:

```html
//my-custom-template.html
<html>
    <head>...</head>
    <body>
        <?php echo $this->renderPage() ?>
    </body>
</html>
```

```php
//in anywhere
(new RBFrameworks\Core\Template(__DIR__.'/my-custom-template.html'))->setPage(__DIR__.'/my-page.php')->render();

//or...
(new RBFrameworks\Core\Template(__DIR__.'/my-custom-template.html'))->setVar(['content' => __DIR__.'/my-page.php', 'anothervar' => 'xyz'])->render();

```
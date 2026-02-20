# Instalação

## 1) Adicione o Router
Você só precisa adicionar ```Utils\Htmx\HtmxRouter.php``` para o $router da aplicação.
Assim, as chamadas para esse router (por padrão será `{httpSite}htmx`) já irão carregar os componentes.

## 2) Configure
Você precisa reviar as configurações no arquivo ```Utils\Htmx\HtmxBootstrap.php```

## 3) Configure
Inclua uma URL onde a dependência do javascript html está localizada.
Você pode fazer isso chamando o códgo php entre as tags head do projeto: ```Utils\Htmx\HtmxBootstrap::getScriptSrc()```

# Utilização

Além das chamadas pela @route, você pode chamar no projeto seus componentes através de:
```Utils\Htmx\Helper\Location::getComponenentFromName("meu-componenet") ```

E o componente em si pode ser criado assim:
```php

new \Utils\Htmx\HtmxComponent('conteudo html do componente ou uma view', 'nome', []);

```

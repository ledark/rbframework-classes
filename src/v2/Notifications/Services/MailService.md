# RBFrameworksReadme

Para enviar um e-mail:

```php
//in anywhere
use RBFrameworks\Core\Notifications\Services\MailService;

$mailerUser = new MailService();
//all config comes from collection mail
$mailerUser->setTemplateDir('path/to/templates/');
$mailerUser->templateExtension = '.html'; //if you need change the default extension
$mailerUser->useTemplate('<p>mensagem do corpo do e-mail</p>', 'assunto do e-mail', 'template_filename_without_extension');
$mailerUser->send();
```

Se precisar mudar o conteudo do template, use:

```html
<!-- meu-template.html -->
Esse é o conteúdo padrão que virá em todos os e-mails.
<blockquote>{body}</blockquote>
A variável body acima será substituída pela mensagem do e-mail.
```

E todas as opções do collection mail são:

```
host
SMTPAuth
username
password
SMTPSecure
port

subject
body
baseuri

e abaixo todos usam um array com [[email => name], [email => name], [email => name]]
    from 
    to
    reply
    cc
    bcc
```


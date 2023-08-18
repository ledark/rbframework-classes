<?php return [

//Authentication
'host'       => 'mail.rbframework.com.br',                     //Set the SMTP server to send through
'SMTPAuth'   => true,                                   //Enable SMTP authentication
'username'   => 'usermail@rbframework.com.br',                     //SMTP username
'password'   => 'very-secret-password',                               //SMTP password
'SMTPSecure' => 'ssl', //PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
'port'       => 465,

//Defauls [email => name]
'from' => [
    'feed@rbframework.com.br' => 'RB Frameworks',
],
'to' => [
    'mestreledark@gmail.com' => 'Ricardo Bermejo',
],
'reply' => [
    'contato@rbframework.com.br' => 'RBFrameworks',
],
'cc' => [
    'ricardocc@rbframework.com.br' => 'Mail CC',
],
'bcc' => [
    'ricardobcc@rbframework.com.br' => 'Mail BCC',
],

//AnotherDefauls
'subject' => date('Y-m-d H:i:s').' Teste de envio automático de email',
'body'    => 'Teste de conteúdo do e-mail com HTML message <b>em bold!</b>',
'baseuri' => 'https://rbframework.com.br/',

];
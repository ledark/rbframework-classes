<?php 

namespace RBFrameworks\Core\Notifications\Services;

use RBFrameworks\Core\Config;
use Notifications\Services\NotificationServiceInterface;
use RBFrameworks\Core\Utils\Replace;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService extends Service {

    public $mail;
    private $errors = [];
    public $template = 'sample';
    public $templateDir = null;
    public $templateExtension = '.html';

    public function __construct() {

        //Create an instance; passing `true` enables exceptions
        $this->mail = new PHPMailer(true);        

        try {

            $this->mail = new PHPMailer(true); //True Enables Exceptions
        
            //Server settings
            $this->mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Change to STMP::DEBUG_SERVER to enable verbose debug output
            $this->mail->isSMTP();                                            //Send using SMTP        
        
            //ThisConfig Changes
            $this->mail->Host       = Config::get('mail.host');         // Specify main and backup SMTP servers
            $this->mail->SMTPAuth   = Config::get('mail.SMTPAuth');     // Enable SMTP authentication
            $this->mail->Username   = Config::get('mail.username');     // SMTP username
            $this->mail->Password   = Config::get('mail.password');     // SMTP password
            $this->mail->SMTPSecure = Config::get('mail.SMTPSecure');   // Enable TLS encryption, `ssl` also accepted
            $this->mail->Port       = Config::get('mail.port');         //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $this->addDefaultRecipients('mail.from');
            $this->addDefaultRecipients('mail.to');
            $this->addDefaultRecipients('mail.reply');
            $this->addDefaultRecipients('mail.cc');
            $this->addDefaultRecipients('mail.bcc');
        
            //Attachments
            //$this->mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        
            //Content
            $this->mail->isHTML(true);                                  //Set email format to HTML
            $this->mail->Subject = Config::get('mail.subject');
            $this->mail->Body    = Config::get('mail.body');
            $this->mail->AltBody = strip_tags(Config::get('mail.body'));      
            
        } catch (Exception $e) {
            $this->errors[] = "Message could not be created. Mailer Error: {$this->mail->ErrorInfo} {$e->getMessage()};";
        } catch (\Exception $e) {
            $this->errors[] = "PHP err exception on created: {$e->getMessage()}";
        }   
        
    }

    public function setTemplateDir(string $templateDir):object {
        $this->templateDir = $templateDir;
        return $this;
    }

    public function getTemplateDir():string {
        if(is_null($this->templateDir)) {
            return __DIR__."/../Templates/";
        }
        return $this->templateDir;
    }

    public function getTemplateExtension():string {
        return $this->templateExtension;
    }

    public function useTemplate(string $message, string $subject = "", string $template = "sample" ):object {
        $this->template = $template;
        ob_start();
        include($this->getTemplateDir().$template.$this->getTemplateExtension());
        $template = ob_get_clean();
        
        $content = (new Replace($template, [
            'title' => $subject,
            'body' => $message,
            'unscribelink' => $this->getUnscribeLink(),
        ]))
            ->whenLiteral( function($matches) { return ''.$matches[1].''; } )
            ->setBrackets(['\[\[', '\]\]'])
            ->render(true);        
        
        $this->mail->Subject = $subject;
        $this->mail->Body    = $content;
        $this->mail->AltBody = strip_tags($this->mail->Body);
        return $this;
    }

    public function asTest():object {
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->isHTML(true);                                  //Set email format to HTML
        $this->mail->Body    = 'Essa é uma mensagem de teste para confirmar o envio do e-mail.';
        $this->mail->AltBody = strip_tags($this->mail->Body);
        return $this;
    }

    private function addDefaultRecipients(string $collection) {
        $recipients = Config::get($collection);
        if(!count($recipients)) return;
        switch($collection) {
            case 'mail.from':
                foreach($recipients as $mail => $name) {
                    if(is_numeric($mail) and is_string($name)) {
                        $mail = $name;
                    }
                    $this->mail->setFrom($mail, $name);
                }
            break;
            case 'mail.to':
                foreach($recipients as $mail => $name) {
                    if(is_numeric($mail) and is_string($name)) {
                        $mail = $name;
                    }
                    $this->mail->addAddress($mail, $name);
                }
            break;
            case 'mail.reply':
                foreach($recipients as $mail => $name) {
                    if(is_numeric($mail) and is_string($name)) {
                        $mail = $name;
                    }
                    $this->mail->addReplyTo($mail, $name);
                }
            break;
            case 'mail.cc':
                foreach($recipients as $mail => $name) {
                    if(is_numeric($mail) and is_string($name)) {
                        $mail = $name;
                    }
                    $this->mail->addCC($mail, $name);
                }
            break;
            case 'mail.bcc':
                foreach($recipients as $mail => $name) {
                    if(is_numeric($mail) and is_string($name)) {
                        $mail = $name;
                    }
                    $this->mail->addBCC($mail, $name);
                }
            break;
        }
    }

    public function getUnscribeLink():string {
        return Config::get('mail.baseuri').'';
    }

    public function getTemplate():string {
        ob_start();
        include($this->getTemplateDir().$this->template.$this->getTemplateExtension());
        $template = ob_get_clean();
        return (new Replace($template, [
            'title' => $this->mail->Subject,
            'body' => $this->mail->Body,
            'unscribelink' => $this->getUnscribeLink(),
        ]))
            ->whenLiteral( function($matches) { return ''.$matches[1].''; } )
            ->setBrackets(['\[\[', '\]\]'])
            ->render(true);
    }

    public function send() {
        try {
            $originalBody = $this->mail->Body;
            $this->mail->Body = $this->getTemplate();
            $this->mail->send();
            $this->mail->Body = $originalBody;
        } catch (Exception $e) {
            $this->errors[] = "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo} {$e->getMessage()};";
        } catch (\Exception $e) {
            $this->errors[] = "PHP err exception: {$e->getMessage()}";
        }
    }

    public function getErrors():array {
        return $this->errors;
    }

}
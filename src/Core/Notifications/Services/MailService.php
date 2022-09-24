<?php 

namespace Core\Notifications\Services;

/*
require("_app/class/vendor/PHPMailer/src/Exception.php");
require("_app/class/vendor/PHPMailer/src/PHPMailer.php");
require("_app/class/vendor/PHPMailer/src/SMTP.php");    
*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Types\File;
use Core\Utils\Replace;

class MailService extends Service {

    public $mail;

    public function __construct() {

        try {

            $this->mail = new PHPMailer(true); //True Enables Exceptions
        
            //Server settings
            $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $this->mail->isSMTP();                                            //Send using SMTP        
        
            //ThisConfig Changes
            $this->mail->Host       = 'mail.rbframework.com.br'          ;  // Specify main and backup SMTP servers
            $this->mail->SMTPAuth   = true                         ;  // Enable SMTP authentication
            $this->mail->Username   = 'user@rbframework.com.br'          ;  // SMTP username
            $this->mail->Password   = 'password'        ;  // SMTP password
            $this->mail->SMTPSecure = 'ssl'                        ;  // Enable TLS encryption, `ssl` also accepted
            $this->mail->Port       = 465                          ;       
        
            //Recipients
            $this->mail->setFrom('feed@bermejo.com.br', 'RBFrameworks');
            $this->mail->addReplyTo('contato@rbframework.com.br', 'RBFrameworks');

            //$this->mail->addAddress('mestreledark@gmail.com', 'Ricardo TestPhpMailer');     //Add a recipient
        //    $this->mail->addAddress('ellen@example.com');               //Name is optional
         //   $this->mail->addCC('cc@example.com');
         //   $this->mail->addBCC('bcc@example.com');
        
         /*
            //Attachments
            $this->mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            $this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        */
        
            //Content
            $this->mail->isHTML(true);                                  //Set email format to HTML
            $this->mail->Subject = 'Novo contato através do Site';
            $this->mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            
            
        } catch (\Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
        
    }

    public function useTemplate(string $message, string $subject = ""):object {
        //claudio.simil@hotmail.com
        $content = new File('template');
        $replacer = new Replace($content->getFileContents(), [
            'subject' => empty($subject) ? $this->mail->Subject : $subject,
            'message' => $message,
        ]);
        $this->mail->Body    = $replacer->render(true);
        $this->mail->AltBody = strip_tags($this->mail->Body);
        return $this;
    }

    public function asTest():object {

        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;

        //Recipients [MultipleProviders]
        //$this->mail->setFrom('feed@rbframework.com.br', 'JFF E-Commerce');
        $this->mail->addAddress('mestreledark@gmail.com', 'Ricardo TestPhpMailer');     //Add a recipient
        $this->mail->addAddress('ricardo@bermejo.com.br');               //Name is optional
        $this->mail->addReplyTo('another@rbframework.com.br', 'RBFrameworks');
        $this->mail->addCC('rbmestre@yahoo.com.br');
        $this->mail->addBCC('dragaodefogo@hotmail.com');
        
    
        /*
        //Attachments
        $this->mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        $this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        */
    
        //Content
        $this->mail->isHTML(true);                                  //Set email format to HTML
        $this->mail->Body    = 'Essa é uma mensagem de teste para confirmar o envio do e-mail.';
        $this->mail->AltBody = strip_tags($this->mail->Body);
        return $this;
    }


    public function send() {
        try {
            ob_start();
            $this->mail->send();
            Debug::log(ob_get_clean());
        } catch (\Exception $e) {
           echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }

}
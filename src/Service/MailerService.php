<?php
require  '../vendor/autoload.php';



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'innatv40@gmail.com';
        $this->mail->Password = 'sjuopmrhbdyzrrlx';
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587;

        $this->mail->setFrom('innatv40@gmail.com', 'OLX-Parsing');
        $this->mail->isHTML(false);
    }

    public function sendEmail($to, $subject, $message)
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $message;

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function sendConfirmationCode($to, $confirmationCode)
    {
        $subject = 'Email Confirmation Code';
        $message = "Your email confirmation code is: $confirmationCode";

        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $message;

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}
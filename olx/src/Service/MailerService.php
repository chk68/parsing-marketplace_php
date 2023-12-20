<?php
namespace MyApp\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../../vendor/autoload.php';


class MailerService
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'email';
        $this->mail->Password = 'pass';
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587;

        $this->mail->setFrom('email', 'OLX-Parsing');
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


    public function sendConfirmationCode($to, $confirmationCode): bool
    {
        $subject = 'Email Confirmation Code';
        $message = "Your email confirmation code is: $confirmationCode";

        return $this->sendEmail($to, $subject, $message);
    }

}
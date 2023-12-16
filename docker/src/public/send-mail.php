<?php

require_once "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendNewAdNotification($userEmail, $adId)
{
    $mail = createMailer();
    $subject = "Subscription completed! ";
    $message = "New ad $adId added.\n";

    return sendEmail($mail, $userEmail, $subject, $message);
}

function sendPriceChangeNotification($userEmail, $adId, $newPrice)
{
    $mail = createMailer();
    $subject = "Warning! Price reduced";
    $message = "The price for $adId has changed. New price: $newPrice UAH\n";

    return sendEmail($mail, $userEmail, $subject, $message);
}

function createMailer()
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'innatv40@gmail.com';
    $mail->Password = 'sjuopmrhbdyzrrlx';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('innatv40@gmail.com', 'OLX-Parsing');
    $mail->isHTML(false);

    return $mail;
}

function sendEmail($mail, $to, $subject, $message)
{
    try {
        $mail->clearAddresses();
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/*//
sendNewAdNotification('user@example.com', '12345');
sendPriceChangeNotification('user@example.com', '12345', '1000');*/


<?php

require_once __DIR__ . "/../Service/MailerService.php";

class NewAdNotificationController
{
    public static function sendNotification($userEmail, $adId)
    {
        $mailer = new MailerService();
        $subject = "Subscription completed!";
        $message = "New ad $adId added.\n";

        return $mailer->sendEmail($userEmail, $subject, $message);
    }



}
<?php

namespace MyApp\Service;

class NewAdNotificationService
{
    public static function sendNotification($userEmail, $adId): bool
    {
        $mailer = new MailerService();
        $subject = "Subscription completed!";
        $message = "New ad $adId added.\n";

        return $mailer->sendEmail($userEmail, $subject, $message);
    }


}
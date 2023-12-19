<?php

namespace MyApp\Controller;

use MyApp\Service\MailerService;

class PriceChangeController
{
    public static function sendNotification($userEmail, $adId, $newAdPrice, $newCurrency): bool
    {
        $mailer = new MailerService();
        $subject = "Warning! Price reduced";
        $message = "The price for $adId has changed. New price: $newAdPrice $newCurrency\n";

        return $mailer->sendEmail($userEmail, $subject, $message);
    }

    public static function sendThankYou($userEmail, $adId): bool
    {
        $mailer = new MailerService();
        $subject = "Thank You!";
        $message = "Thank you for using our service. The price for ad $adId remains unchanged.";

        return $mailer->sendEmail($userEmail, $subject, $message);
    }

}
<?php
// File: PriceChangeController.php

class PriceChangeController
{
    public static function sendNotification($userEmail, $adId, $newAdPrice, $newCurrency)
    {
        $mailer = new MailerService();
        $subject = "Warning! Price reduced";
        $message = "The price for $adId has changed. New price: $newAdPrice $newCurrency\n";

        return $mailer->sendEmail($userEmail, $subject, $message);
    }
}